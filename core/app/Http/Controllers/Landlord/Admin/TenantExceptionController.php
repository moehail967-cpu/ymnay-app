<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Actions\Tenant\ReGenerateTenant;
use App\Helpers\FlashMsg;
use App\Helpers\ResponseMessage;
use App\Helpers\SanitizeInput;
use App\Http\Controllers\Controller;
use App\Models\TenantException;
use App\Models\Testimonial;
use App\Models\Themes;
use Illuminate\Http\Request;
use App\Models\PaymentLogs;
use App\Models\Tenant;
use App\Helpers\Payment\DatabaseUpdateAndMailSend\LandlordPricePlanAndTenantCreate;
//use DB;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Jobs;
use Illuminate\Support\Facades\Artisan;
use App\Exceptions\CustomTenantException;
use App\Exceptions\CustomDatabaseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class TenantExceptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function website_issues()
    {
        $all_issues = TenantException::where(['domain_create_status'=> 0, 'seen_status' => 0])->get();
        return view('landlord.admin.user-website-issues.all-issues', compact('all_issues'));
    }

    public function generate_domain(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $id = $request->id;
        $exception = TenantException::findOrFail($id);
        $tenant = Tenant::find($exception->tenant_id);
        $payment_log = PaymentLogs::where('tenant_id', $tenant->id)->first();

        $validated = [
            'account_status' => $payment_log->status,
            'subs_tenant_id' => $tenant->id
        ];

        $reassign_object = new ReGenerateTenant($validated);
        $response = $reassign_object->regenerateTenant();

        if (!empty($response))
        {
            return back()->withErrors($response);
        }

        return back()->with(FlashMsg::explain('success', 'Tenant Regenerated successfully'));
    }


    public function manual_database(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'database_name' => 'required|string|max:191',
                'exception_id' => 'required',
                'database_user_name' => 'nullable|string|max:191',
                'database_user_password' => 'nullable|string|max:191',
            ]);

            $this->extractRequestData($request);

            // Find and validate tenant
            $exception = TenantException::findOrFail($this->exceptionId);
            $tenant = Tenant::find($exception->tenant_id);

            if (!$tenant) {
                throw CustomTenantException::notFound($exception->tenant_id);
            }

            // Validate payment log
            $paymentLog = $this->validatePaymentLog($tenant->id);

            // Process tenant setup
            $this->processTenantSetup($paymentLog, $tenant);

            // Update tenant database configuration
            $this->updateTenantDatabaseConfig($tenant);

            // Create domain and migrate
            $this->createDomainAndMigrate($tenant);

            // Seed database
            $this->seedTenantDatabase($tenant);

            // Finalize setup
            $this->finalizeSetup($exception, $paymentLog);

            // Delete all tenant exceptions for this tenant since operation was successful
            $this->cleanupTenantExceptions($tenant->id);

            return response()->success(ResponseMessage::SettingsSaved('Database and domain create success'));

        } catch (ValidationException $e) {
            return response()->danger(__('Validation failed: ') . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return response()->danger(__('Required record not found'));
        } catch (CustomTenantException $e) {
            // Custom tenant exception handling with automatic reporting
            return $e->render($request);
        } catch (CustomDatabaseException $e) {
            // Custom database exception handling with automatic reporting
            return $e->render($request);
        } catch (\Exception $e) {
            Log::error('Manual database creation failed', [
                'exception_id' => $this->exceptionId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->danger(__('An unexpected error occurred. Please try again.'));
        }
    }

    private function extractRequestData(Request $request)
    {
        $this->exceptionId = $request->exception_id;
        $this->databaseName = trim($request->database_name);
        $this->databaseUserName = trim($request->database_user_name);
        $this->databaseUserPassword = $request->database_user_password;
    }

    private function validatePaymentLog($tenantId)
    {
        $paymentLog = PaymentLogs::where('tenant_id', $tenantId)->first();

        if (!$paymentLog) {
            throw CustomTenantException::creationFailed('Payment log not found', $tenantId);
        }

        return $paymentLog;
    }

    private function processTenantSetup($paymentLog, $tenant)
    {
        try {
            $paymentData = ['order_id' => $paymentLog->id];

            LandlordPricePlanAndTenantCreate::update_tenant($paymentData);
            LandlordPricePlanAndTenantCreate::update_database($paymentLog->id, $paymentLog->transaction_id);

        } catch (\Exception $e) {
            throw CustomTenantException::creationFailed('Failed to update tenant setup: ' . $e->getMessage(), $tenant->id);
        }
    }

    private function updateTenantDatabaseConfig($tenant)
    {
        try {
            DB::transaction(function () use ($tenant) {
                $currentTenant = DB::table('tenants')->where('id', $tenant->id)->first();

                if (!$currentTenant) {
                    throw CustomTenantException::notFound($tenant->id);
                }

                $format = (array) json_decode($currentTenant->data, true);
                $format['tenancy_db_name'] = $this->databaseName;
                $format['tenancy_db_username'] = $this->databaseUserName;
                $format['tenancy_db_password'] = $this->databaseUserPassword;

                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update(['data' => json_encode($format)]);
            });

        } catch (CustomTenantException $e) {
            throw $e; // Re-throw tenant exceptions
        } catch (\Exception $e) {
            throw CustomDatabaseException::queryFailed(
                'tenant configuration update',
                $e->getMessage(),
                $this->databaseName
            );
        }
    }

    private function createDomainAndMigrate($tenant)
    {
        try {
            // Create domain
            $domain = $tenant->getTenantKey() . '.' . env('CENTRAL_DOMAIN');

            // Check if domain already exists
            $existingDomain = $tenant->domains()->where('domain', $domain)->first();

            if (!$existingDomain) {
                $tenant->domains()->create(['domain' => $domain]);
            }

            // Run migrations
            $command = 'tenants:migrate --force --tenants=' . $tenant->id;
            $exitCode = Artisan::call($command);

            if ($exitCode !== 0) {
                throw CustomTenantException::migrationFailed($tenant->id, $command);
            }

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'occupied by another tenant')) {
                throw CustomTenantException::domainOccupied($domain, $tenant->id);
            }
            if (str_contains($e->getMessage(), 'Access denied')) {
                throw CustomDatabaseException::accessDenied($this->databaseName, $this->databaseUserName);
            }
            if (str_contains($e->getMessage(), 'Connection refused') || str_contains($e->getMessage(), 'Connection timed out')) {
                throw CustomDatabaseException::serverUnavailable($this->databaseName);
            }

            throw CustomTenantException::creationFailed('Domain creation or migration failed: ' . $e->getMessage(), $tenant->id);
        }
    }

    private function seedTenantDatabase($tenant)
    {
        try {
            $exitCode = Artisan::call('tenants:seed', [
                '--tenants' => $tenant->getTenantKey(),
                '--force' => true
            ]);

            if ($exitCode !== 0) {
                throw CustomTenantException::seedingFailed($tenant->id);
            }

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                // This is acceptable - data already exists
                Log::info('Tenant database already seeded', ['tenant_id' => $tenant->id]);
                return;
            }

            throw CustomTenantException::seedingFailed($tenant->id);
        }
    }

    private function finalizeSetup($exception, $paymentLog)
    {
        try {
            DB::transaction(function () use ($exception, $paymentLog) {
                $exception->domain_create_status = 1;
                $exception->seen_status = 1;
                $exception->save();

                LandlordPricePlanAndTenantCreate::tenant_create_event_with_credential_mail($paymentLog->id, false);
                LandlordPricePlanAndTenantCreate::send_order_mail($paymentLog->id);
            });

        } catch (\Exception $e) {
            throw CustomTenantException::creationFailed('Failed to finalize setup: ' . $e->getMessage());
        }
    }

    private function cleanupTenantExceptions($tenantId)
    {
        try {
            DB::transaction(function () use ($tenantId) {
                // Get all exceptions for this tenant before deleting for logging
                $exceptions = TenantException::where('tenant_id', $tenantId)->get();

                if ($exceptions->isNotEmpty()) {
                    // Log details of what we're cleaning up
                    // Log::info('Cleaning up tenant exceptions after successful operation', [
                    //     'tenant_id' => $tenantId,
                    //     'exception_count' => $exceptions->count(),
                    //     'exception_ids' => $exceptions->pluck('id')->toArray()
                    // ]);

                    // Delete all exceptions for this tenant
                    $deletedCount = TenantException::where('tenant_id', $tenantId)->delete();

                    // Log::info('Successfully cleaned up tenant exceptions', [
                    //     'tenant_id' => $tenantId,
                    //     'deleted_count' => $deletedCount
                    // ]);
                } else {
                    // Log::info('No tenant exceptions found to cleanup', [
                    //     'tenant_id' => $tenantId
                    // ]);
                }
            });

        } catch (\Exception $e) {
            // Log the error but don't fail the main operation since it was successful
            // Log::error('Failed to cleanup tenant exceptions after successful operation', [
            //     'tenant_id' => $tenantId,
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);

            // Fallback cleanup attempt
            try {
                $deletedCount = TenantException::where('tenant_id', $tenantId)->delete();
                // Log::info('Fallback cleanup successful', [
                //     'tenant_id' => $tenantId,
                //     'deleted_count' => $deletedCount
                // ]);
            } catch (\Exception $fallbackError) {
                // Log::error('Fallback cleanup also failed', [
                //     'tenant_id' => $tenantId,
                //     'error' => $fallbackError->getMessage()
                // ]);
            }
        }
    }


    /**
     * Bulk delete tenant exceptions
     *
     * @param Request $request
     */
    /**
     */
    public function deleteException( $id)
    {
        try {
            $exceptionId = $id;
            $exception = TenantException::findOrFail($exceptionId);
            $exception->delete();

            return response()->success(ResponseMessage::delete(__('Exception deleted successfully')));
        }catch (\Exception $e) {
            return response()->error($e->getMessage());
        }

    }

}
