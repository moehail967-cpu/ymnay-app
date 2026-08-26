<?php

namespace App\Helpers\Payment;

use App\Helpers\PaymentGatewayRenderHelper;
use App\Models\PaymentGateway;
use Modules\CommissionManage\Entities\CommissionSelectedGateway;
use Modules\CommissionManage\Entities\CommissionSetting;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;

class PaymentGatewayCredential
{
    private static function getGatewayFromDatabase(string $gatewayName)
    {
        $activeGateways = PaymentGatewayRenderHelper::getActiveGateways();
        // Check if using landlord gateways
        $useLandlord = !empty($activeGateways) && ($activeGateways[0]['from_landlord'] ?? false);
        if ($useLandlord) {
            return CommissionSelectedGateway::where('name', $gatewayName)->first();
        }

        return PaymentGateway::where('name', $gatewayName)->first();
    }
    public static function site_global_currency(){
        return  get_static_option('site_global_currency');
    }

    public static function exchange_rate_usd_to_usd(){
        $global_currency = get_static_option('site_global_currency');
        $usd_exchange_rate = get_static_option('site_' . strtolower($global_currency) . '_to_usd_exchange_rate');
        $checked_currency_rate = empty($usd_exchange_rate) ? 100 : $usd_exchange_rate;
        return $checked_currency_rate;
    }

    public static function exchange_rate_usd_to_inr(){

        $global_currency = get_static_option('site_global_currency');
        $inr_exchange_rate = get_static_option('site_' . strtolower($global_currency) . '_to_inr_exchange_rate');
        $checked_currency_rate = empty($inr_exchange_rate) ? 83 : $inr_exchange_rate;
        return $checked_currency_rate;
    }

    public static function exchange_rate_usd_to_bdt() {

        $global_currency = get_static_option('site_global_currency');
        $bdt_exchange_rate = get_static_option('site_' . strtolower($global_currency) . '_to_bdt_exchange_rate');
        return empty($bdt_exchange_rate) ? 105 : $bdt_exchange_rate;
    }

    public static function exchange_rate_usd_to_ngn(){
        $global_currency = get_static_option('site_global_currency');
        $ngn_exchange_rate = get_static_option('site_' . strtolower($global_currency) . '_to_ngn_exchange_rate');
        $checked_currency_rate = empty($ngn_exchange_rate) ? 83 : $ngn_exchange_rate;
        return $checked_currency_rate;
    }

    public static function exchange_rate_usd_to_idr(){
        $global_currency = get_static_option('site_global_currency') ?? "IDR";
        $idr_exchange_rate = get_static_option('site_' . strtolower($global_currency) . '_to_idr_exchange_rate');
        $checked_currency_rate = empty($idr_exchange_rate) ? 83 : $idr_exchange_rate;
        return $checked_currency_rate;
    }

    public static function exchange_rate_usd_to_zar(){
        return get_static_option('site_usd_to_zar_exchange_rate');
    }

    public static function exchange_rate_usd_to_brl(){
        $global_currency = get_static_option('site_global_currency');
        $brl_exchange_rate = get_static_option('site_' . strtolower($global_currency) . '_to_brl_exchange_rate');
        $checked_currency_rate = empty($brl_exchange_rate) ? 83 : $brl_exchange_rate;
        return $checked_currency_rate;
    }

    public static function exchange_rate_usd_to_myr(){
        $global_currency = get_static_option('site_global_currency');
        $myr_exchange_rate = get_static_option('site_' . strtolower($global_currency) . '_to_myr_exchange_rate');
        $checked_currency_rate = empty($myr_exchange_rate) ? 83 : $myr_exchange_rate;
        return $checked_currency_rate;
    }


    public static function get_paypal_credential() : object
    {
        //todo:: check for landlord commission system settings, then based on this get payment gateway data

        $paypal_credential_from_database = self::getGatewayFromDatabase('paypal');
        $decoded = json_decode($paypal_credential_from_database->credentials);
        $mode = $paypal_credential_from_database->test_mode;

        $checked_client_id = $mode == 1 ? $decoded->sandbox_client_id : $decoded->live_client_id;
        $checked_client_secret = $mode == 1 ? $decoded->sandbox_client_secret : $decoded->live_client_secret;
        $checked_app_id = $mode == 1 ? $decoded->sandbox_app_id : 'xxx';
        $test_mode = $paypal_credential_from_database->test_mode == 1;

        $paypal = XgPaymentGateway::paypal();
        $paypal->setClientId($checked_client_id);
        $paypal->setClientSecret($checked_client_secret);
        $paypal->setAppId($checked_app_id);
        $paypal->setCurrency(self::site_global_currency());
        $paypal->setEnv($test_mode);
        $paypal->setExchangeRate(self::exchange_rate_usd_to_usd());

        return $paypal;
    }

    public static function get_paytm_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('paytm');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $merchant_id = $decoded->merchant_mid ?? '';
        $merchant_key = $decoded->merchant_key ?? '';
        $merchant_website = $decoded->merchant_website ?? '';
        $channel = $decoded->channel ?? '';
        $industry_type = $decoded->industry_type ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $paytm = XgPaymentGateway::paytm();
        $paytm->setMerchantId($merchant_id);
        $paytm->setMerchantKey($merchant_key);
        $paytm->setMerchantWebsite($merchant_website);
        $paytm->setChannel($channel);
        $paytm->setIndustryType($industry_type);
        $paytm->setCurrency(self::site_global_currency());
        $paytm->setEnv($test_mode);
        $paytm->setExchangeRate(self::exchange_rate_usd_to_inr());

        return $paytm;
    }

    public static function get_stripe_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('stripe');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $public_key = $decoded->public_key ?? '';
        $secret_key = $decoded->secret_key ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $stripe = XgPaymentGateway::stripe();
        $stripe->setSecretKey($secret_key);
        $stripe->setPublicKey($public_key);
        $stripe->setCurrency(self::site_global_currency());
        $stripe->setEnv($test_mode);
        $stripe->setExchangeRate(self::exchange_rate_usd_to_inr());

        return $stripe;
    }

    public static function get_razorpay_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('razorpay');
        $gate = self::getGatewayFromDatabase('razorpay');
//        dd($gate);
//        dd($paypal_credential_from_database->credentials);
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $api_key = $decoded->api_key ?? '';
        $api_secret = $decoded->api_secret ?? '';
        $webhook_secret = $decoded->webhook_secret ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $razorpay = XgPaymentGateway::razorpay();
        $razorpay->setApiKey($api_key);
        $razorpay->setApiSecret($api_secret);
        $razorpay->setWebhookSecret($webhook_secret);
        $razorpay->setCurrency(self::site_global_currency());
        $razorpay->setEnv($test_mode);
        $razorpay->setExchangeRate(self::exchange_rate_usd_to_inr());

        return $razorpay;
    }

    public static function get_paystack_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('paystack');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $public_key = $decoded->public_key ?? '';
        $secret_key = $decoded->secret_key ?? '';
        $marchant_email = $decoded->merchant_email ?? '';

        $test_mode = $paypal_credential_from_database->test_mode;

        $paystack = XgPaymentGateway::paystack();
        $paystack->setPublicKey($public_key);
        $paystack->setSecretKey($secret_key);
        $paystack->setMerchantEmail($marchant_email);
        $paystack->setCurrency(self::site_global_currency());
        $paystack->setEnv($test_mode);
        $paystack->setExchangeRate(self::exchange_rate_usd_to_ngn());

        return $paystack;
    }

    public static function get_mollie_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('mollie');
        $decoded = json_decode($paypal_credential_from_database->credentials);
        $public_key = $decoded->public_key ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $mollie = XgPaymentGateway::mollie();
        $mollie->setApiKey($public_key);
        $mollie->setCurrency(self::site_global_currency());
        $mollie->setEnv($test_mode);
        $mollie->setExchangeRate(self::exchange_rate_usd_to_inr());

        return $mollie;
    }

    public static function get_flutterwave_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('flutterwave');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $public_key = $decoded->public_key ?? '';
        $secret_key = $decoded->secret_key ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $flutterwave = XgPaymentGateway::flutterwave();
        $flutterwave->setPublicKey($public_key);
        $flutterwave->setSecretKey($secret_key);
        $flutterwave->setCurrency(self::site_global_currency());
        $flutterwave->setEnv($test_mode);
        $flutterwave->setExchangeRate(self::exchange_rate_usd_to_ngn());

        return $flutterwave;
    }

    public static function get_midtrans_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('midtrans');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $server_key = $decoded->server_key ?? '';
        $client_key = $decoded->client_key ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $midtrans = XgPaymentGateway::midtrans();
        $midtrans->setClientKey($client_key);
        $midtrans->setServerKey($server_key);
        $midtrans->setCurrency(self::site_global_currency());
        $midtrans->setEnv($test_mode);
        $midtrans->setExchangeRate(self::exchange_rate_usd_to_idr());

        return $midtrans;
    }

    public static function get_payfast_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('payfast');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $merchant_id = $decoded->merchant_id ?? '';
        $merchant_key = $decoded->merchant_key ?? '';
        $passphrase = $decoded->passphrase ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $payfast = XgPaymentGateway::payfast();
        $payfast->setMerchantId($merchant_id);
        $payfast->setMerchantKey($merchant_key);
        $payfast->setPassphrase($passphrase);
        $payfast->setCurrency(self::site_global_currency());
        $payfast->setEnv($test_mode);
        $payfast->setExchangeRate((float)self::exchange_rate_usd_to_inr());

        return $payfast;
    }

    public static function get_cashfree_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('cashfree');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $app_id = $decoded->app_id ?? '';
        $secret_key = $decoded->secret_key ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $cashfree = XgPaymentGateway::cashfree();
        $cashfree->setAppId($app_id);
        $cashfree->setSecretKey($secret_key);
        $cashfree->setCurrency(self::site_global_currency());
        $cashfree->setEnv($test_mode);
        $cashfree->setExchangeRate(self::exchange_rate_usd_to_inr());

        return $cashfree;
    }

    public static function get_awdpay_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('awdpay');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $private_key = $decoded->private_key ?? '';
        $logo_url = $decoded->logo_url ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $cashfree = XgPaymentGateway::awdpay();
        $cashfree->setPrivateKey($private_key);
        $cashfree->setLogoUrl($logo_url);
        $cashfree->setEnv($test_mode);
        $cashfree->setCurrency(self::site_global_currency());

        return $cashfree;
    }

    public static function get_instamojo_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('instamojo');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $client_id = $decoded->client_id ?? '';
        $client_secret = $decoded->client_secret ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $instamojo = XgPaymentGateway::instamojo();
        $instamojo->setClientId($client_id);
        $instamojo->setSecretKey($client_secret);
        $instamojo->setCurrency(self::site_global_currency());
        $instamojo->setEnv($test_mode);
        $instamojo->setExchangeRate(self::exchange_rate_usd_to_inr());

        return $instamojo;
    }

    public static function get_marcadopago_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('marcadopago');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $client_id = $decoded->client_id ?? '';
        $client_secret = $decoded->client_secret ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $marcadopago = XgPaymentGateway::mercadopago();
        $marcadopago->setClientId($client_id);
        $marcadopago->setClientSecret($client_secret);
        $marcadopago->setCurrency(self::site_global_currency());
        $marcadopago->setEnv($test_mode);
        $marcadopago->setExchangeRate(self::exchange_rate_usd_to_brl());

        return $marcadopago;
    }

    public static function get_squareup_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('squareup');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $location_id = $decoded->location_id ?? '';
        $access_token = $decoded->access_token ?? '';
        $setApplicationId = $decoded->setApplicationId ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $squareup = XgPaymentGateway::squareup();
        $squareup->setLocationId($location_id);
        $squareup->setAccessToken($access_token);
        $squareup->setApplicationId($setApplicationId);
        $squareup->setCurrency(self::site_global_currency());
        $squareup->setEnv($test_mode);
        $squareup->setExchangeRate(self::exchange_rate_usd_to_inr());

        return $squareup;
    }

    public static function get_cinetpay_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('cinetpay');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $setAppKey = $decoded->apiKey ?? '';
        $setSiteId = $decoded->site_id ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $cinetpay = XgPaymentGateway::cinetpay();
        $cinetpay->setAppKey($setAppKey);
        $cinetpay->setSiteId($setSiteId);
        $cinetpay->setCurrency(self::site_global_currency());
        $cinetpay->setEnv($test_mode);
        $cinetpay->setExchangeRate(self::exchange_rate_usd_to_inr());

        return $cinetpay;
    }

    public static function get_kineticpay_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('kineticpay');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $merchant_key = $decoded->merchant_key ?? '';
        $bank = $decoded->bank ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $kineticpay = XgPaymentGateway::kineticpay();
        $kineticpay->setMerchantKey($merchant_key);
        $kineticpay->setBank($bank);
        $kineticpay->setEnv($test_mode);
        $kineticpay->setCurrency(self::site_global_currency());
        $kineticpay->setExchangeRate(self::exchange_rate_usd_to_myr());

        return $kineticpay;
    }

    public static function get_paytabs_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('paytabs');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $setProfileId = $decoded->profile_id ?? '';
        $setRegion = $decoded->region ?? '';
        $setServerKey = $decoded->server_key ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $paytabs = XgPaymentGateway::paytabs();
        $paytabs->setProfileId($setProfileId);
        $paytabs->setRegion($setRegion);
        $paytabs->setServerKey($setServerKey);
        $paytabs->setCurrency(self::site_global_currency());
        $paytabs->setEnv($test_mode);
        $paytabs->setExchangeRate(self::exchange_rate_usd_to_usd());

        return $paytabs;
    }

    public static function get_billplz_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('billplz');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $setKey = $decoded->key ?? '';
        $setVersion = $decoded->version ?? '';
        $setXsignature = $decoded->x_signature ?? '';
        $setCollectionName = $decoded->collection_name ?? '';
        $test_mode = $paypal_credential_from_database->test_mode == 1;

        $billplz = XgPaymentGateway::billplz();
        $billplz->setKey($setKey);
        $billplz->setVersion($setVersion);
        $billplz->setXsignature($setXsignature);
        $billplz->setCollectionName($setCollectionName);
        $billplz->setCurrency(self::site_global_currency());
        $billplz->setEnv($test_mode);
        $billplz->setExchangeRate(self::exchange_rate_usd_to_myr());

        return $billplz;
    }

    public static function get_zitopay_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('zitopay');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $setUsername = $decoded->username ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $zitopay = XgPaymentGateway::zitopay();
        $zitopay->setUsername($setUsername);
        $zitopay->setCurrency(self::site_global_currency());
        $zitopay->setEnv($test_mode);
        $zitopay->setExchangeRate(self::exchange_rate_usd_to_inr()); // if INR not set as currency

        return $zitopay;
    }

    public static function get_iyzipay_credential() : object
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('iyzipay');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $setSecretKey = $decoded->secret_key ?? '';
        $setApiKey = $decoded->api_key ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $iyzipay = XgPaymentGateway::iyzipay();
        $iyzipay->setSecretKey($setSecretKey);
        $iyzipay->setApiKey($setApiKey);
        $iyzipay->setEnv($test_mode);
        $iyzipay->setCurrency(self::site_global_currency());

        return $iyzipay;
    }

    public static function get_sslcommerz_credential() : object
    {
        $sslcommerz_credential_from_database = self::getGatewayFromDatabase('sslcommerz');
        $decoded = json_decode($sslcommerz_credential_from_database->credentials);

        $setStoreId = $decoded->store_id ?? '';
        $setStorePasswd = $decoded->store_password ?? '';
        $test_mode = $sslcommerz_credential_from_database->test_mode;

        $sslcommerz = XgPaymentGateway::sslcommerz();
        $sslcommerz->setStoreId($setStoreId);
        $sslcommerz->setStorePasswd($setStorePasswd);
        $sslcommerz->setEnv($test_mode);
        $sslcommerz->setCurrency(self::site_global_currency());
        $sslcommerz->setExchangeRate(self::exchange_rate_usd_to_bdt()); // if BDT not set as currency

        return $sslcommerz;
    }

    public static function get_toyyibpay_credential()
    {
        $paypal_credential_from_database = self::getGatewayFromDatabase('toyyibpay');
        $decoded = json_decode($paypal_credential_from_database->credentials);

        $setSecretKey = $decoded->client_secret ?? '';
        $setCategoryCode = $decoded->category_code ?? '';
        $test_mode = $paypal_credential_from_database->test_mode;

        $toyyibpay = XgPaymentGateway::toyyibpay();
        $toyyibpay->setUserSecretKey($setSecretKey);
        $toyyibpay->setCategoryCode($setCategoryCode);
        $toyyibpay->setEnv($test_mode);
        $toyyibpay->setCurrency(self::site_global_currency());
        $toyyibpay->setExchangeRate(self::exchange_rate_usd_to_inr()); //only support MYR Currency

        return $toyyibpay;
    }
}
