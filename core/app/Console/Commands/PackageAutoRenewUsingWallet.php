<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Http\Services\WalletService;
use Throwable;

class PackageAutoRenewUsingWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:auto-renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::channel('package_auto_renew')->info(' Package auto-renew started at ' . now());
        try {
            WalletService::renew_package_from_wallet();

            Log::channel('package_auto_renew')->info('Package auto-renew completed successfully at ' . now());
        } catch (Throwable $e) {
            Log::channel('package_auto_renew')->error('Package auto-renew failed: ' . $e->getMessage(), [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
