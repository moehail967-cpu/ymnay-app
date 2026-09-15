<?php
/**
 * Isolated integration fixture: real candidate controllers/models/provisioner, Laravel validator,
 * transactions, Stancl DB switching and MySQL. Theme list, auth, tenant migration/seed content,
 * file queue and mail are explicit test adapters. NOT full application boot, OTP or browser E2E.
 */
if (getenv('YMNAY_TEST_ALLOW_DISPOSABLE_DB') !== '1'
    || getenv('YMNAY_TEST_DB') !== 'ymnay_onboarding_test'
    || getenv('YMNAY_TEST_HOST') !== '127.0.0.1') {
    throw new RuntimeException('Only the explicitly configured disposable local test database is allowed.');
}
require getenv('YMNAY_TEST_FIXTURE') . '/vendor/autoload.php';

use App\Models\{Admin, PaymentLogs, PricePlan, StaticOptionCentral, StoreOnboardingRequest, Tenant, User};
use App\Services\Onboarding\StoreOnboardingProvisioner;
use Illuminate\Config\Repository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Cookie, DB, Event, Schema};
use Illuminate\Support\Str;

class FixtureState {
    public static ?User $user = null;
    public static bool $failDomain = false, $failSeed = false, $failMail = false;
    public static int $databaseCalls = 0, $migrationCalls = 0, $seedCalls = 0, $fileCalls = 0, $mailCalls = 0;
}
function getAllThemeSlug() { return ['theme-a', 'theme-b']; }
function getPricePlanBasedAllThemeData($slugs) { return array_map(fn ($slug) => (object) ['slug' => $slug], $slugs); }
function tenant_url_with_protocol($domain) { return 'https://' . $domain; }
function update_static_option($key, $value) {
    if (!tenancy()->initialized) throw new RuntimeException('Fixture option write escaped tenant context.');
    return DB::connection('tenant')->table('static_options')->updateOrInsert(['option_name' => $key], ['option_value' => $value]);
}

$core = dirname(__DIR__, 4) . '/core';
$app = new Application($core);
$app->instance('env', 'testing');
$app->instance('config', new Repository([
    'app' => ['env' => 'testing', 'debug' => false, 'locale' => 'en', 'fallback_locale' => 'en',
        'timezone' => 'UTC', 'url' => 'https://example.invalid', 'key' => 'base64:' . base64_encode(random_bytes(32)), 'cipher' => 'AES-256-CBC'],
    'database' => ['default' => 'central', 'connections' => ['central' => [
        'driver' => 'mysql', 'host' => getenv('YMNAY_TEST_HOST'), 'port' => 3306,
        'database' => getenv('YMNAY_TEST_DB'), 'username' => 'root', 'password' => getenv('YMNAY_TEST_PASSWORD'),
        'charset' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'prefix' => '', 'strict' => true,
    ]]],
    'tenancy' => ['tenant_model' => Tenant::class, 'domain_model' => \Stancl\Tenancy\Database\Models\Domain::class,
        'id_generator' => \Stancl\Tenancy\UUIDGenerator::class, 'central_domains' => ['example.invalid'], 'routes' => false, 'features' => [],
        'bootstrappers' => [\Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class],
        'database' => ['central_connection' => 'central', 'prefix' => 'ymnayqa_', 'suffix' => '',
            'managers' => ['mysql' => \Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class]],
        'migration_parameters' => ['--path' => ['fixture-only'], '--realpath' => true]],
    'cache' => ['default' => 'array', 'stores' => ['array' => ['driver' => 'array']]],
    'logging' => ['default' => 'fixture', 'channels' => ['fixture' => ['driver' => 'monolog', 'handler' => \Monolog\Handler\StreamHandler::class, 'handler_with' => ['stream' => 'php://stderr']]]],
    'session' => ['driver' => 'array', 'lifetime' => 120, 'encrypt' => false, 'cookie' => 'ymnay_fixture', 'path' => '/', 'http_only' => true, 'same_site' => 'lax'],
    'view' => ['paths' => [], 'compiled' => sys_get_temp_dir()],
    'hashing' => ['driver' => 'bcrypt', 'bcrypt' => ['rounds' => 4]],
    'auth' => ['defaults' => ['guard' => 'web'], 'guards' => ['web' => ['driver' => 'session', 'provider' => 'users']],
        'providers' => ['users' => ['driver' => 'eloquent', 'model' => User::class]]],
    'activitylog' => ['enabled' => false],
]));
\Illuminate\Support\Facades\Facade::setFacadeApplication($app);
foreach ([
    \Illuminate\Events\EventServiceProvider::class, \Illuminate\Filesystem\FilesystemServiceProvider::class,
    \Illuminate\Log\LogServiceProvider::class, \Illuminate\Database\DatabaseServiceProvider::class,
    \Illuminate\Routing\RoutingServiceProvider::class, \Illuminate\Translation\TranslationServiceProvider::class,
    \Illuminate\Validation\ValidationServiceProvider::class, \Illuminate\Cache\CacheServiceProvider::class,
    \Illuminate\Encryption\EncryptionServiceProvider::class, \Illuminate\Hashing\HashServiceProvider::class,
    \Illuminate\Session\SessionServiceProvider::class, \Illuminate\View\ViewServiceProvider::class,
    \Spatie\Permission\PermissionServiceProvider::class, \Spatie\Activitylog\ActivitylogServiceProvider::class,
    \Stancl\Tenancy\TenancyServiceProvider::class,
] as $provider) $app->register($provider);
$app->instance('request', Request::create('https://example.invalid/create-store'));
$app->boot();
config(['activitylog.enabled' => false]);
class_alias(Str::class, 'Str');
Auth::swap(new class {
    public function guard($name) { return $this; }
    public function user() { return FixtureState::$user; }
    public function id() { return $this->user()?->id; }
    public function check() { return (bool) $this->user(); }
});
Cookie::swap(new class { public function queue(...$args) {} public function forget($name) { return $name; } });
Request::macro('validate', function (array $rules, ...$args) {
    return \Illuminate\Support\Facades\Validator::make($this->all(), $rules, ...$args)->validate();
});
$app['router']->get('/create-store', fn () => 'fixture')->name('landlord.store.onboarding');
$app['router']->getRoutes()->refreshNameLookups();
$app->instance('migrator', new class { public function getMigrationFiles($paths) { return ['qa_fixture' => __FILE__]; } });

// Candidate event routing, including the actual opt-in TenantCreated closure.
$provider = new \App\Providers\TenancyServiceProvider($app);
foreach ($provider->events() as $event => $listeners) {
    foreach ($listeners as $listener) {
        if ($listener instanceof \Stancl\JobPipeline\JobPipeline) $listener = $listener->toListener();
        Event::listen($event, $listener);
    }
}
Event::listen(\App\Events\TenantRegisterEvent::class, \App\Listeners\TenantDomainCreate::class);
Event::listen(\Stancl\Tenancy\Events\CreatingDomain::class, function () {
    if (FixtureState::$failDomain) throw new RuntimeException('Injected domain failure before insert');
});
Event::listen(\Stancl\Tenancy\Events\DatabaseCreated::class, function () { FixtureState::$databaseCalls++; });

// Explicit adapters: tenant schema/content is synthetic, never the project's customer seeders.
\Illuminate\Support\Facades\Artisan::swap(new class {
    public function call($command, $args) {
        if ($command !== 'tenants:migrate') throw new RuntimeException('Unexpected command in fixture');
        FixtureState::$migrationCalls++;
        $tenant = Tenant::findOrFail($args['--tenants'][0]);
        $tenant->run(function () {
            $s = Schema::connection('tenant');
            $s->create('migrations', function (Blueprint $t) { $t->id(); $t->string('migration'); $t->integer('batch'); });
            $s->create('admins', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('username'); $t->string('email'); $t->string('password'); $t->timestamps(); });
            $s->create('roles', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('guard_name'); $t->timestamps(); });
            $s->create('model_has_roles', function (Blueprint $t) { $t->unsignedBigInteger('role_id'); $t->string('model_type'); $t->unsignedBigInteger('model_id'); });
            $s->create('static_options', function (Blueprint $t) { $t->id(); $t->string('option_name')->unique(); $t->text('option_value')->nullable(); });
            DB::connection('tenant')->table('migrations')->insert(['migration' => 'qa_fixture', 'batch' => 1]);
        });
        return 0;
    }
});
$app->bindMethod([\App\Jobs\TenantSeedDatabaseJob::class, 'handle'], function ($job) {
    FixtureState::$seedCalls++;
    $tenant = (new ReflectionProperty($job, 'tenant'))->getValue($job);
    $tenant->run(function () {
        $admin = Admin::create(['name' => 'Fixture Admin', 'username' => 'fixture-admin', 'email' => 'admin@example.invalid', 'password' => 'unused-fixture-hash']);
        if (FixtureState::$failSeed) throw new RuntimeException('Injected failure after seed insert');
        $role = DB::connection('tenant')->table('roles')->insertGetId(['name' => 'Super Admin', 'guard_name' => 'web']);
        DB::connection('tenant')->table('model_has_roles')->insert(['role_id' => $role, 'model_type' => Admin::class, 'model_id' => $admin->id]);
    });
});
$app->bindMethod([\App\Jobs\TenantFileSycnForNewTenant::class, 'handle'], function () { FixtureState::$fileCalls++; });
$app->bindMethod([\App\Jobs\NewShopCreatedEmailNotificationJob::class, 'handle'], function () {
    FixtureState::$mailCalls++;
    if (FixtureState::$failMail) throw new RuntimeException('Injected mail failure');
});

// Disposable central schema. Only the actual onboarding migration is executed from the application.
Schema::create('users', function (Blueprint $t) {
    $t->id(); foreach (['name','email','username'] as $key) $t->string($key);
    $t->integer('email_verified')->default(1); $t->integer('has_subdomain')->default(0); $t->softDeletes(); $t->timestamps();
});
Schema::create('price_plans', function (Blueprint $t) {
    $t->id(); $t->string('title'); $t->string('price'); $t->integer('type'); $t->integer('status');
    $t->boolean('has_trial'); $t->integer('trial_days'); $t->timestamps();
});
Schema::create('plan_themes', function (Blueprint $t) { $t->id(); $t->unsignedBigInteger('plan_id'); $t->string('theme_slug'); $t->integer('status'); $t->timestamps(); });
Schema::create('tenants', function (Blueprint $t) {
    $t->string('id')->primary(); $t->json('data')->nullable(); $t->timestamps(); $t->unsignedBigInteger('user_id')->nullable();
    foreach (['theme_slug','unique_key','start_date','expire_date'] as $key) $t->string($key)->nullable();
    $t->unsignedBigInteger('renewal_payment_log_id')->nullable();
});
Schema::create('domains', function (Blueprint $t) { $t->id(); $t->string('domain')->unique(); $t->string('tenant_id'); $t->timestamps(); });
Schema::create('tenant_unique_keys', function (Blueprint $t) { $t->id(); $t->string('tenant_id'); $t->string('unique_key'); $t->timestamps(); });
Schema::create('static_option_centrals', function (Blueprint $t) { $t->id(); $t->string('option_name')->unique(); $t->text('option_value')->nullable(); $t->string('unique_key')->nullable(); $t->timestamps(); });
Schema::create('payment_logs', function (Blueprint $t) {
    $t->id(); foreach (['email','name','package_name','package_price','tenant_id','status','payment_status','track','start_date','expire_date','theme_slug'] as $key) $t->string($key)->nullable();
    $t->unsignedBigInteger('package_id')->nullable(); $t->unsignedBigInteger('user_id')->nullable(); $t->integer('is_renew')->default(0); $t->timestamps();
});
(require $core . '/database/migrations/2026_09_14_000001_create_store_onboarding_requests_table.php')->up();
