<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\StaticOptionCentral;
use Illuminate\Support\Facades\Log;
use Modules\CpanelAutomation\Http\Services\CpanelHelper;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Events\DatabaseDeleted;
use Stancl\Tenancy\Events\DeletingDatabase;
use Stancl\Tenancy\Jobs\DeleteDatabase;

class DeleteDatabaseWithFallback extends DeleteDatabase
{


    public function handle()
    {
        event(new DeletingDatabase($this->tenant));

        $cpanelAutomationStatus = StaticOptionCentral::where('option_name', '_cpanel_automation_status')
            ->first()?->option_value;

        if ($cpanelAutomationStatus) {
            try {
                $cpanelHost = StaticOptionCentral::where('option_name', '_cpanel_url')->first()?->option_value;
                $cpanelUsername = StaticOptionCentral::where('option_name', '_cpanel_username')->first()?->option_value;
                $cpanelPassword = StaticOptionCentral::where('option_name', '_cpanel_access_token')->first()?->option_value;

                $cpanel = new CpanelHelper(
                    cpanelUrl: $cpanelHost,
                    cpanelToken: $cpanelPassword,
                    cpanelUser: $cpanelUsername
                );

                $tenancyPrefix = config('tenancy.database.prefix', '');
                $tenancySuffix = config('tenancy.database.suffix', '');
                $baseDatabaseName = $tenancyPrefix . $this->tenant->getTenantKey() . $tenancySuffix;

                $cpanelPrefix = $cpanelUsername . '_';

                $dbName = $this->tenant->getInternal('db_name') ?? $cpanelPrefix . $baseDatabaseName;
                $userName = $this->tenant->getInternal('db_username') ?? $cpanelPrefix . substr($this->tenant->getTenantKey(), 0, 10);

                // CpanelHelper::deleteDatabase/deleteDatabaseUser prepend cpanelUsername_ internally,
                // so strip it here to avoid double-prefix (e.g. dgexyz_dgexyz_...) which causes silent no-op
                $dbNameForApi = str_starts_with($dbName, $cpanelPrefix) ? substr($dbName, strlen($cpanelPrefix)) : $dbName;
                $userNameForApi = str_starts_with($userName, $cpanelPrefix) ? substr($userName, strlen($cpanelPrefix)) : $userName;

                $cpanel->deleteDatabaseUser($userNameForApi);
                $cpanel->deleteDatabase($dbNameForApi);

                Log::info('CPanel database deletion successful', [
                    'tenant_id' => $this->tenant->id,
                    'db_name' => $dbName,
                    'db_user' => $userName,
                ]);
            } catch (\Exception $e) {
                Log::error('CPanel database deletion failed: ' . $e->getMessage(), [
                    'tenant_id' => $this->tenant->id,
                ]);
                throw $e;
            }
        } else {
            try {
                $this->tenant->database()->manager()->deleteDatabase($this->tenant);
            } catch (\Exception $e) {
                Log::error('Default database deletion failed: ' . $e->getMessage(), [
                    'tenant_id' => $this->tenant->id,
                ]);
                throw $e;
            }
        }

        event(new DatabaseDeleted($this->tenant));
    }
}
