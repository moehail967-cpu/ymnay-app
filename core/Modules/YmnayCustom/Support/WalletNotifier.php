<?php

namespace Modules\YmnayCustom\Support;

use App\Mail\BasicMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\SmsGateway\Http\Traits\OtpGlobalTrait;

class WalletNotifier
{
    use OtpGlobalTrait;

    public static function email(?string $email, string $subject, string $message): void
    {
        if (!$email) return;
        try {
            Mail::to($email)->send(new BasicMail($message, $subject));
        } catch (\Throwable $e) {
            Log::warning('Ymnay wallet email notification failed', ['error' => $e->getMessage()]);
        }
    }

    public static function sms(?string $phone, string $message, ?int $orderId = null): void
    {
        if (!$phone) return;
        try {
            (new self())->sendSms([$phone, $message, $orderId ?? 0], 'notify', 'order');
        } catch (\Throwable $e) {
            Log::warning('Ymnay wallet SMS notification failed', ['error' => $e->getMessage()]);
        }
    }
}
