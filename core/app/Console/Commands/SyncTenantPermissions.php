<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncTenantPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-tenant-permissions';

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
        $newPermissions = [
            'digital_product',
            'digital-product-list',
            'digital-product-create',
            'digital-product-edit',
            'digital-product-delete',
            'digital-product-settings',
            'digital-product-reviews',
            'digital-product-category-list',
            'digital-product-subcategory-list',
            'digital-product-childcategory-list',
            'digital-product-tax-list',
            'digital-product-language-list'
        ];

        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn('⚠️ No tenants found.');
            return;
        }

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);

            $this->info("🔄 Syncing permissions for tenant: {$tenant->id}");

            foreach ($newPermissions as $perm) {
                Permission::updateOrCreate(['name' => $perm, 'guard_name' => 'admin']);
            }

            // Assign permissions to Super Admin
            $role = Role::where('name','Super Admin')->first();
//            $role = Role::where('name', 'Super Admin')->first();
            if ($role) {
                $role->givePermissionTo($newPermissions);
                $this->info(" Assigned new permissions to Super Admin for tenant: {$tenant->id}");
            } else {
                $this->warn("⚠️ No Super Admin role found in tenant: {$tenant->id}");
            }

            tenancy()->end();
        }

        $this->info("🎉 Done! All tenants now have the new permissions.");
    }
}
