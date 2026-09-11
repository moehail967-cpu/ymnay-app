<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    private string $secretKey;
    private string $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct()
    {
        $this->secretKey = config('services.recaptcha.secret_key', '');
    }

    public function verify(?string $token): bool
    {
        if (empty($this->secretKey) || empty($token)) {
            return false;
        }

        $response = Http::asForm()->post($this->verifyUrl, [
            'secret'   => $this->secretKey,
            'response' => $token,
        ]);

        return $response->successful() && $response->json('success') === true;
    }
}
