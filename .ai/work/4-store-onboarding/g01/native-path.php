<?php

declare(strict_types=1);

use App\Events\TenantRegisterEvent;
use App\Models\Admin;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

if (getenv('YMNAY_G01') !== '1' || getenv('APP_ENV') !== 'testing') {
    fwrite(STDERR, "G01 native-path verification is restricted to the disposable testing environment.\n");
    exit(2);
}

require __DIR__.'/../../../../core/vendor/autoload.php';
$app = require __DIR__.'/../../../../core/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$plan = PricePlan::query()->where('status', 1)->firstOrFail();
$user = User::create([
    'name' => 'G01 Native Path User',
    'email' => 'g01-native@example.test',
    'username' => 'g01_native_user',
    'mobile' => '966500000009',
    'password' => Hash::make('G01-Isolated-Password!'),
    'email_verified' => 1,
]);

$payment = PaymentLogs::create([
    'email' => $user->email,
    'name' => $user->name,
    'package_name' => $plan->title,
    'package_price' => $plan->price,
    'package_id' => $plan->id,
    'user_id' => $user->id,
    'tenant_id' => 'g01-native-store',
    'status' => 'complete',
    'payment_status' => 'complete',
    'is_renew' => 0,
    'track' => Str::random(10),
    'start_date' => now()->format('d-m-Y H:i:s'),
    'expire_date' => now()->addMonth()->format('d-m-Y H:i:s'),
    'theme_slug' => 'hexfashion',
]);

// This deliberately uses the pre-onboarding event contract. No gateway, charge,
// external account or production service participates in this synthetic check.
event(new TenantRegisterEvent($user, 'g01-native-store', 'hexfashion'));

$tenant = Tenant::findOrFail('g01-native-store');
if (! $tenant->domain || ! $tenant->unique_key || (int) $tenant->user_id !== (int) $user->id) {
    throw new RuntimeException('Native tenant pipeline did not create domain, owner and login key.');
}

try {
    tenancy()->initialize($tenant);
    foreach (['migrations', 'admins', 'roles', 'permissions', 'pages'] as $table) {
        if (! DB::connection('tenant')->getSchemaBuilder()->hasTable($table)) {
            throw new RuntimeException("Native tenant table is missing: {$table}");
        }
    }
    $admin = Admin::on('tenant')->first();
    if (! $admin || ! $admin->hasRole('Super Admin')) {
        throw new RuntimeException('Native tenant administrator is not ready.');
    }
    $tenantDatabase = DB::connection('tenant')->getDatabaseName();
} finally {
    tenancy()->end();
}

echo json_encode([
    'tenant' => $tenant->id,
    'tenant_database' => $tenantDatabase,
    'payment_fixture' => $payment->id,
    'gateway_or_charge_used' => false,
    'native_pipeline' => true,
    'synthetic_only' => true,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
