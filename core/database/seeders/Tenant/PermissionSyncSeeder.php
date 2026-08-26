<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class PermissionSyncSeeder extends Seeder
{
    public function run(): void
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
            'digital-product-language-list',
        ];

        $createdCount = 0;

        foreach ($newPermissions as $permission) {
            $exists = Permission::where('name', $permission)
                ->where('guard_name', 'admin')
                ->exists();

            if (!$exists) {
                Permission::create([
                    'name' => $permission,
                    'guard_name' => 'admin',
                ]);
                $createdCount++;
            }
        }

        if ($createdCount > 0) {
            Log::info(" PermissionSyncSeeder: {$createdCount} new permissions added successfully.");
        } else {
            Log::info(' PermissionSyncSeeder: All permissions already exist — no new permissions added.');
        }
    }
}
