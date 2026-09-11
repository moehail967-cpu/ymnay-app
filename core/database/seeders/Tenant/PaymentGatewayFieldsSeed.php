<?php

namespace Database\Seeders\Tenant;


use App\Helpers\ImageDataSeedingHelper;
use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewayFieldsSeed extends Seeder
{
    public function run()
    {
        // Gateway credentials are configured by the store owner.
        $paymentGateways = [
            [
                'name' => 'paypal',
                'image' => '465',
                'description' => 'if your currency is not available in paypal, it will convert you currency value to USD value based on your currency exchange rate.',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'sandbox_client_id' => null,
                    'sandbox_client_secret' => null,
                    'sandbox_app_id' => null,
                    'live_client_id' => null,
                    'live_client_secret' => null,
                    'live_app_id' => null,
                ])
            ],
            [
                'name' => 'paytm',
                'image' => '312',
                'description' => 'if your currency is not available in paytm, it will convert you currency value to INR value based on your currency exchange rate.',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'merchant_key' => null,
                    'merchant_mid' => null,
                    'merchant_website' => null,
                    'channel' => null,
                    'industry_type' => null,
                ])
            ],
            [
                'name' => 'stripe',
                'image' => '315',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'public_key' => null,
                    'secret_key' => null,
                ])
            ],
            [
                'name' => 'razorpay',
                'image' => '313',
                'description' => 'if your currency is not available in Razorpay, it will convert you currency value to INR value based on your currency exchange rate.',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'api_key' => null,
                    'api_secret' => null,
                    'webhook_secret' => null,
                ])
            ],
            [
                'name' => 'paystack',
                'image' => '311',
                'description' => 'if your currency is not available in Paystack, it will convert you currency value to NGN value based on your currency exchange rate.',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'public_key' => null,
                    'secret_key' => null,
                    'merchant_email' => null,
                ])
            ],
            [
                'name' => 'mollie',
                'image' => '307',
                'description' => 'if your currency is not available in mollie, it will convert you currency value to USD value based on your currency exchange rate.',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'public_key' => null,
                ])
            ],
            [
                'name' => 'midtrans',
                'image' => '305',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'merchant_id' => null,
                    'server_key' => null,
                    'client_key' => null,
                ])
            ],
            [
                'name' => 'cashfree',
                'image' => '316',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'app_id' => null,
                    'secret_key' => null,
                ])
            ],
            [
                'name' => 'instamojo',
                'image' => '314',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'client_id' => null,
                    'client_secret' => null,
                    'username' => null,
                    'password' => null,
                ])
            ],
            [
                'name' => 'marcadopago',
                'image' => '306',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'client_id' => null,
                    'client_secret' => null,
                ])
            ],
            [
                'name' => 'zitopay',
                'image' => '441',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'username' => null,
                ])
            ],
            [
                'name' => 'squareup',
                'image' => '442',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'location_id' => null,
                    'access_token' => null,
                ])
            ],
            [
                'name' => 'cinetpay',
                'image' => '443',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'apiKey' => null,
                    'site_id' => null,
                ])
            ],
            [
                'name' => 'kineticpay',
                'image' => '961',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'merchant_key' => '',
                    'bank' => ''
                ])
            ],
            [
                'name' => 'paytabs',
                'image' => '444',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'profile_id' => null,
                    'region' => null,
                    'server_key' => null,
                ])
            ],
            [
                'name' => 'billplz',
                'image' => '445',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'key' => null,
                    'version' => null,
                    'x_signature' => null,
                    'collection_name' => null,
                ])
            ],
            [
                'name' => 'toyyibpay',
                'image' => '446',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'client_secret' => null,
                    'category_code' => null,
                ])
            ],
            [
                'name' => 'flutterwave',
                'image' => '447',
                'description' => 'if your currency is not available in flutterwave, it will convert you currency value to USD value based on your currency exchange rate.',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'public_key' => null,
                    'secret_key' => null,
                    'secret_hash' => null,
                ])
            ],
            [
                'name' => 'payfast',
                'image' => '308',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'merchant_id' => null,
                    'merchant_key' => null,
                    'passphrase' => null,
                    'itn_url' => null,
                ])
            ],
            [
                'name' => 'iyzipay',
                'image' => '963',
                'description' => '',
                'status' => 0,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'secret_key' => 'Manual Payment',
                    'api_key' => 'Manual Payment Here',
                ])
            ],
            [
                'name' => 'sslcommerz',
                'image' => '960',
                'description' => '',
                'status' => 0,
                'test_mode' => 1,
                'credentials'=> json_encode([
                    'store_id' => '',
                    'store_password'=> ''
                ])
            ],
            [
                'name' => 'awdpay',
                'image' => '962',
                'description' => '',
                'status' => 0,
                'test_mode' => 1,
                'credentials'=> json_encode([
                    'private_key' => '',
                    'logo_url'=> ''
                ])
            ],
            [
                'name' => 'manual_payment',
                'image' => '310',
                'description' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => json_encode([
                    'name' => 'Manual Payment',
                    'description' => 'Manual Payment Here',
                ])
            ],
        ];

        foreach ($paymentGateways as $gateway) {
            PaymentGateway::create($gateway);
        }
    }
}
