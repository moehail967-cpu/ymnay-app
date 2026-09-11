<?php

namespace App\Console\Commands;

use App\Models\PaymentLogs;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillTenantRenewalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-tenant-renewal-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting backfill process...');

        $tenants = Tenant::all();
        $updatedCount = 0;

        foreach ($tenants as $tenant) {
            $payment_log = PaymentLogs::where('tenant_id', $tenant->id)
                ->where('payment_status', 'complete')
                ->latest('id')
                ->first();

            if (!$payment_log) {
                $this->warn("No completed payment log found for tenant: {$tenant->id}");
                continue;
            }

            // Determine if renewal happened after expiry
            $renewal_after_expire = 0;
            if ($payment_log->expire_date && Carbon::parse($payment_log->expire_date)->isPast()) {
                $renewal_after_expire = 1;
            }

            // Format renewal_at safely
            $renewal_at = null;
            if (!empty($payment_log->start_date)) {
                try {
                    $renewal_at = Carbon::parse($payment_log->start_date)->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    Log::error("Invalid date format for tenant {$tenant->id}: " . $payment_log->start_date);
                }
            }

            DB::table('tenants')->where('id', $tenant->id)->update([
                'renewal_at' => $renewal_at,
                'renewal_after_expire' => $renewal_after_expire,
                'price_plan_id' => $payment_log->package_id,
                'renewal_payment_log_id' => $payment_log->id,
                'updated_at' => now(),
            ]);

            $updatedCount++;
            $this->info("Updated tenant {$tenant->id}");
        }

        $this->info(" Backfill completed successfully. {$updatedCount} tenants updated.");
        Log::info("Tenant backfill completed. {$updatedCount} tenants updated.");

        return Command::SUCCESS;
    }
}
