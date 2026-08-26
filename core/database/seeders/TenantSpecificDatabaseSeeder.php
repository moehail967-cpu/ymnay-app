<?php

namespace Database\Seeders;


use Database\Seeders\Tenant\PaymentGatewayFieldsSeed;
use Database\Seeders\Tenant\ProductSeed;
use Illuminate\Database\Seeder;

class TenantSpecificDatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CitySeeder::class,
        ]);

    }
}
