"""Install only the test dependency closure, pinned to the application's lockfile.
No application dependency files, .env files, scripts or production credentials are used.
"""
import json
import os
from pathlib import Path

root = Path(__file__).resolve().parents[4]
lock = json.loads((root / 'core/composer.lock').read_text())
packages = {p['name']: p for p in lock['packages'] + lock.get('packages-dev', [])}
names = {'laravel/framework', 'stancl/tenancy', 'laravel/sanctum', 'spatie/laravel-permission',
         'spatie/laravel-activitylog', 'phpunit/phpunit'}
pending = list(names)
while pending:
    for name in packages[pending.pop()].get('require', {}):
        if name in packages and name not in names:
            names.add(name)
            pending.append(name)
fixture = Path(os.environ['YMNAY_TEST_FIXTURE'])
fixture.mkdir(parents=True, exist_ok=True)
config = {'name': 'ymnay/onboarding-test-fixture', 'type': 'project',
          'require': {name: packages[name]['version'] for name in sorted(names)},
          'minimum-stability': 'dev', 'prefer-stable': True,
          'config': {'allow-plugins': False},
          'autoload': {'psr-4': {'App\\': str(root / 'core/app') + '/',
                                  'Database\\Seeders\\': str(root / 'core/database/seeders') + '/'}}}
(fixture / 'composer.json').write_text(json.dumps(config, indent=2) + '\n')
print('Prepared isolated test dependency closure:', len(names), 'locked packages')
