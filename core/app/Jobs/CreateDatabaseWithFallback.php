<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\CpanelAutomation\Http\Services\CpanelHelper;
use Stancl\Tenancy\Database\DatabaseManager;
use Stancl\Tenancy\Jobs\CreateDatabase;
use App\Models\StaticOptionCentral;

class CreateDatabaseWithFallback extends CreateDatabase
{
    public function handle(DatabaseManager $databaseManager)
    {
        $cpanelEnabled = StaticOptionCentral::where(
            'option_name',
            '_cpanel_automation_status'
        )->value('option_value');

        // Use cPanel ONLY when enabled
        if ($cpanelEnabled) {
            $this->createDatabaseViaCpanel();
            return;
        }

        // Local / VPS environment
        parent::handle($databaseManager);
    }

    protected function createDatabaseViaCpanel(): void
    {
        try {
            $cpanelHost = StaticOptionCentral::where('option_name', '_cpanel_url')
                ->value('option_value');

            $cpanelUser = StaticOptionCentral::where('option_name', '_cpanel_username')
                ->value('option_value');

            $cpanelToken = StaticOptionCentral::where('option_name', '_cpanel_access_token')
                ->value('option_value');


            if (!$cpanelHost || !$cpanelUser || !$cpanelToken) {
                throw new \Exception('CPanel credentials missing');
            }

            // Get the base database name from tenancy config (e.g., "nazmart_tenant_db_hhh1")
            $baseDatabaseName = $this->tenant->database()->getName();
//            $baseDatabaseName = config('tenancy.database.prefix') . $this->tenant->id;


            // Generate credentials
            // CpanelHelper adds cpanelUser_ prefix, so we only pass the suffix
            $dbPassword = Str::random(16);
            $dbUserSuffix = Str::random(10);

            $cpanel = new CpanelHelper(
                cpanelUrl: $cpanelHost,
                cpanelToken: $cpanelToken,
                cpanelUser: $cpanelUser
            );

            // Create DB + User (CpanelHelper adds cpanelUser_ prefix internally)
            $cpanel->createDatabase($baseDatabaseName);
            $cpanel->createDatabaseUser($dbUserSuffix, $dbPassword);
            $cpanel->assignUserToDatabase($dbUserSuffix, $baseDatabaseName);

            // The actual MySQL names include cPanel prefix
            $dbUser = $cpanelUser . '_' . $dbUserSuffix;
            $databaseName = $cpanelUser . '_' . $baseDatabaseName;

            Log::info("Creating tenant DB user", [
                'db_user' => $dbUser,
                'db_name' => $databaseName,
                'tenant_id' => $this->tenant->id,
            ]);

            // Persist credentials using tenancy internal key format
            // Store the ACTUAL database name (with cPanel prefix) for connection
            $this->tenant->setInternal('db_name', $databaseName);
            $this->tenant->setInternal('db_username', $dbUser);
            $this->tenant->setInternal('db_password', $dbPassword);
            $this->tenant->save();

            Log::info("Tenant DB created via cPanel", [
                'tenant_id' => $this->tenant->id,
                'db' => $databaseName,
                'db_user' => $dbUser,
            ]);
        } catch (\Throwable $e) {
            Log::error('cPanel DB creation failed', [
                'tenant_id' => $this->tenant->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
