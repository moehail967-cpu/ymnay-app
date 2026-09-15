<?php
/** Actual checkpoint implementation; no Laravel, DB, network or production dependencies. */
require __DIR__ . '/../../../../core/app/Services/Onboarding/RecoveryRequired.php';
require __DIR__ . '/../../../../core/app/Services/Onboarding/ProvisioningStages.php';

use App\Services\Onboarding\ProvisioningStages;
use App\Services\Onboarding\RecoveryRequired;

$results = [];
function check(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
}
function test(string $name, Closure $body): void {
    global $results;
    try { $body(); $results[] = ['case' => $name, 'result' => 'PASS']; }
    catch (Throwable $e) { $results[] = ['case' => $name, 'result' => 'FAIL', 'error' => $e->getMessage()]; }
}
function runner(array &$db): ProvisioningStages {
    return new ProvisioningStages($db, function ($states) use (&$db) { $db = $states; });
}

test('completed non-repeatable seed is never replayed', function () {
    $db = []; $calls = 0;
    runner($db)->run('seed', function () use (&$calls) { $calls++; });
    runner($db)->run('seed', function () use (&$calls) { $calls++; });
    check($calls === 1 && $db['seed'] === 'done', 'Seed was replayed');
});
test('failure before domain resumes only missing stages', function () {
    $db = []; $creates = 0; $domains = 0; $ready = false; $seed = 0;
    runner($db)->run('database', function () use (&$creates) { $creates++; });
    try { runner($db)->run('domain', function () { throw new RuntimeException('injected unavailable dependency'); }, true); }
    catch (RuntimeException $e) { check($db['domain'] === 'running', 'Failure checkpoint missing'); }
    $r = runner($db);
    $r->run('database', function () use (&$creates) { $creates++; });
    $r->run('domain', function () use (&$domains, &$ready) { $domains++; $ready = true; }, true, function () use (&$ready) { return $ready; });
    $r->run('seed', function () use (&$seed) { $seed++; });
    check($creates === 1 && $domains === 1 && $seed === 1 && $db['domain'] === 'done', 'Recovery repeated or skipped a stage');
});
test('side effect completed but checkpoint lost is reconciled', function () {
    $db = ['domain' => 'running']; $calls = 0;
    runner($db)->run('domain', function () use (&$calls) { $calls++; }, true, fn () => true);
    check($calls === 0 && $db['domain'] === 'done', 'Did not reconcile existing domain');
});
test('uncertain partial seeding requires review without rerun', function () {
    $db = []; $calls = 0;
    try { runner($db)->run('seed', function () use (&$calls) { $calls++; throw new RuntimeException('after an insert'); }); }
    catch (RuntimeException $e) { /* injected failure */ }
    try { runner($db)->run('seed', function () use (&$calls) { $calls++; }); throw new RuntimeException('Unsafe seed was allowed'); }
    catch (RecoveryRequired $e) { check($e->stage === 'seed' && $calls === 1, 'Bad recovery boundary'); }
});
test('missing login key is repaired without replaying seed', function () {
    $db = ['seed' => 'done', 'login_key' => 'done']; $keys = 0; $ready = false;
    $r = runner($db);
    $r->run('seed', fn () => throw new RuntimeException('seed replay'));
    $r->run('login_key', function () use (&$ready, &$keys) { $ready = true; $keys++; }, true, function () use (&$ready) { return $ready; });
    check($keys === 1 && $db['login_key'] === 'done', 'Key not repaired');
});
test('failed readiness probe cannot mark a stage done', function () {
    $db = [];
    try { runner($db)->run('domain', fn () => null, true, fn () => false); throw new LogicException('Probe ignored'); }
    catch (RuntimeException $e) { check($db['domain'] === 'running', 'False done checkpoint'); }
});
test('checkpoint persistence failure prevents side effects', function () {
    $calls = 0; $r = new ProvisioningStages([], fn () => throw new RuntimeException('storage unavailable'));
    try { $r->run('database', function () use (&$calls) { $calls++; }); }
    catch (RuntimeException $e) { /* expected */ }
    check($calls === 0, 'Side effect ran before checkpoint persistence');
});
test('lost done checkpoint prevents uncertain destructive replay', function () {
    $db = []; $calls = 0;
    $r = new ProvisioningStages([], function ($states) use (&$db) {
        if ($states['seed'] === 'done') throw new RuntimeException('commit unavailable');
        $db = $states;
    });
    try { $r->run('seed', function () use (&$calls) { $calls++; }); } catch (RuntimeException $e) {}
    try { runner($db)->run('seed', function () use (&$calls) { $calls++; }); }
    catch (RecoveryRequired $e) { check($calls === 1, 'Uncertain seed replayed'); return; }
    throw new RuntimeException('Review required was not surfaced');
});
test('uncertain migration with completed schema is reconciled', function () {
    $db = ['migrations' => 'running'];
    runner($db)->run('migrations', fn () => throw new RuntimeException('migration replay'), false, fn () => true);
    check($db['migrations'] === 'done', 'Schema reconciliation failed');
});
test('uncertain incomplete migration is not blindly replayed', function () {
    $db = ['migrations' => 'running'];
    try { runner($db)->run('migrations', fn () => throw new RuntimeException('migration replay'), false, fn () => false); }
    catch (RecoveryRequired $e) { check($e->stage === 'migrations', 'Wrong stage'); return; }
    throw new RuntimeException('Unsafe DDL replay allowed');
});
test('uncertain cPanel provisioning is not silently retried', function () {
    $db = ['database' => 'running'];
    try { runner($db)->run('database', fn () => throw new RuntimeException('cPanel replay'), false, fn () => false); }
    catch (RecoveryRequired $e) { check($e->stage === 'database', 'Wrong recovery boundary'); return; }
    throw new RuntimeException('cPanel replay allowed');
});
test('lost database checkpoint with real readiness skips creation', function () {
    $db = ['database' => 'running'];
    runner($db)->run('database', fn () => throw new RuntimeException('duplicate database'), true, fn () => true);
    check($db['database'] === 'done', 'Existing database not reconciled');
});
echo json_encode(['scope' => 'Actual ProvisioningStages class with in-memory checkpoint storage; NOT SQL/Laravel/E2E', 'results' => $results], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
exit(count(array_filter($results, fn ($r) => $r['result'] !== 'PASS')) ? 1 : 0);
