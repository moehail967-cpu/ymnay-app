<?php

namespace App\Helpers;

use App\Enums\StatusEnums;
use App\Facades\ModuleDataFacade;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\CommissionManage\Entities\CommissionSelectedGateway;
use Modules\CommissionManage\Entities\CommissionSetting;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;

class PaymentGatewayRenderHelper
{
    public static function listOfPaymentGateways(bool $is_price_plan = false)
    {
        $plan_based_payment_gateway = tenant_plan_payment_gateway_list();
        $payment_gateway_list = PaymentGateway::where('status', StatusEnums::PUBLISH);
        if (!empty($plan_based_payment_gateway)) // For tenant
        {
            $payment_gateway_list->whereIn('name', $plan_based_payment_gateway);
        }

        $payment_gateway_list = $payment_gateway_list->select(['name', 'image'])->get();

        $payment_gateway_list = !empty($payment_gateway_list) ? $payment_gateway_list->toArray() : $payment_gateway_list;

        //todo append payment gateway name from modules
        $modules_payment_gateway = ! tenant() ? getAllPaymentGatewayListWithImage($is_price_plan) : getAllTenantPaymentGatewayListWithImage($plan_based_payment_gateway);

        return !empty($modules_payment_gateway) ? array_merge($payment_gateway_list, $modules_payment_gateway) : $payment_gateway_list;
    }

    public static function renderCurrentBalanceForm()
    {
        $output = '<div class="current-balance-wrapper">';
        $output .= '<input type="checkbox" name="selected_payment_gateway" id="current_balance_gateway" class="mr-2 current_balance_selected_gateway">';
        $output .= '<label for="current_balance_gateway">' . __('Deposit From Current Balance') . '</label>';
        $output .= '</div>';
        return $output;
    }

    public static function renderWalletForm()
    {
        $output = '<div class="wallet-payment-gateway-wrapper">';
        $output .= '<input type="checkbox" name="selected_payment_gateway" id="wallet_selected_payment_gateway" class="mr-2 wallet_selected_payment_gateway">';
        $output .= '<label for="wallet_selected_payment_gateway">' . __('Order From Wallet') . '</label>';
        $output .= '</div>';
        return $output;
    }

    public static function renderPaymentGatewayForForm(array $skip_gateway = [])
    {
        $output = '<div class="payment-gateway-wrapper payment_getway_image">';

        $output .= '<input type="hidden" name="selected_payment_gateway" id="order_from_user_wallet" value="' . get_static_option('site_default_payment_gateway') . '">';

        $all_gateway = array_map(function ($gateway) {
            $logo_id = get_static_option($gateway['name']."_logo");
            if (!empty($logo_id)) {
                $gateway['image'] = $logo_id;
            }

            $gateway['has_logo'] = !empty($logo_id);
            return $gateway;
        }, self::listOfPaymentGateways());

        $output .= '<ul>';
        foreach ($all_gateway as $gateway) {
            if(!empty($skip_gateway) && in_array($gateway['name'], $skip_gateway))
            {
                continue;
            }

            if ($gateway['name'] == 'manual_payment')
            {
                $manual_payment_gateway = PaymentGateway::where(['status' => StatusEnums::PUBLISH, 'name' => $gateway['name']])->first();
                $description = json_decode($manual_payment_gateway->credentials);
                $description = $description->description;
            }

            $class = (get_static_option('site_default_payment_gateway') == $gateway['name']) ? 'class="selected"' : '';
            $output .= '<li data-gateway="' . $gateway['name'] . '" ' . $class . ' data-description="'.(isset($description) ? $description : '').'"><div class="img-select">';

            if (array_key_exists('module', $gateway) && !$gateway['has_logo'])
            {
                $output .= '<img src="'.loadPaymentGatewayLogo(moduleName: $gateway['module'], gatewayName: $gateway['name']).'"';
            } else {
                $output .= render_image_markup_by_attachment_id($gateway['image']);
            }
            $output .= '</div></li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        //extra field data for payment gateway
        $output .= '<div class="payment_gateway_extra_field_information_wrap">';
        if (!empty(get_static_option('manual_payment_gateway'))) {
            $output .= '<div class="manual_payment_gateway_extra_field">
                            <div class="form-group">
                                <div class="label mt-3 mb-2">' . get_static_option('site_manual_payment_name') . __('Receipt') . '</div>
                                    <input type="file" name="manual_payment_image" class="form-control" style="line-height: 1.15">
                                </div>
                            <div class="manual_description">' . get_static_option('site_manual_payment_description') . '</div>
                       </div>';
        }
        //todo write code for all module extra info markup
        $output .= renderAllPaymentGatewayExtraInfoBlade();
        $output .= '</div>';

        return $output;
    }


    /**
     * Detect whether the tenant should use landlord gateways.
     */
    public static function shouldUseLandlordGateways(): bool
    {
        $setting = CommissionSetting::first();
        $result = $setting && $setting->payment_gateway_source === 'landlord_gateway';
        return $result;
    }
    /**
     * Return the list of gateways depending on the configuration.
     */
    public static function getActiveGateways(): array
    {
        $setting = CommissionSetting::first();

        if (!$setting) {
            return self::getTenantGateways();
        }

        if ($setting->payment_gateway_source === 'landlord_gateway') {
            return self::getLandlordGateways($setting);
        }
        return self::getTenantGateways();
    }
    public static function renderPaymentGatewayForUseLandlordGateway(array $gateways = [], array $skip_gateways = [], string $context = 'payment'): string
    {
        if (empty($gateways)) {
            $gateways = self::getActiveGateways();
        }
        $output = '<div class="payment-gateway-wrapper payment_getway_image">';

        // Different input name based on context
        $inputName = ($context === 'withdraw') ? 'selected_payment_gateway' : 'selected_payment_gateway';
        $output .= '<input type="hidden" name="' . $inputName . '" id="order_from_user_wallet" value="' . get_static_option('site_default_payment_gateway') . '">';
        $output .= '<ul>';

        foreach ($gateways as $gateway) {
            if (!empty($skip_gateways) && in_array($gateway['name'], $skip_gateways)) {
                continue;
            }

            // Decode credentials for ALL gateways (not just manual_payment)
            $description = '';
            if (!empty($gateway['credentials'])) {
                if (is_string($gateway['credentials'])) {
                    $gateway['credentials'] = json_decode($gateway['credentials'], true);
                }

                // Handle manual_payment description
                if ($gateway['name'] === 'manual_payment' && isset($gateway['credentials']['description'])) {
                    $description = $gateway['credentials']['description'];
                }
            }

            $isDefault = (get_static_option('site_default_payment_gateway') === $gateway['name']);
            $class = $isDefault ? 'class="selected"' : '';

            $output .= '<li data-gateway="' . $gateway['name'] . '" ' . $class . ' data-description="' . e($description) . '">';
            $output .= '<div class="img-select">';

            if (array_key_exists('module', $gateway) && !$gateway['has_logo'])
            {
                $output .= '<img src="'.loadPaymentGatewayLogo(moduleName: $gateway['module'], gatewayName: $gateway['name']).'"';
            } else {
                $output .= render_image_markup_by_attachment_id_landlord($gateway['image']);
            }
            $output .= '</div></li>';


//            $gatewayDisplayName = ucwords(str_replace('_', ' ', $gateway['name']));
//            $output .= '<span class="gateway-text p-2">' . e($gatewayDisplayName) . '</span>';
//            $output .= '</div></li>';
        }

        $output .= '</ul></div>';

        // Manual + Extra info section (only for payment context, not withdraw)
        if ($context === 'payment') {
            $output .= '<div class="payment_gateway_extra_field_information_wrap">';
            if (!empty(get_static_option('manual_payment_gateway'))) {
                $output .= '<div class="manual_payment_gateway_extra_field">
                <div class="form-group">
                    <div class="label mt-3 mb-2">' . get_static_option('site_manual_payment_name') . __(' Receipt') . '</div>
                    <input type="file" name="manual_payment_image" class="form-control" style="line-height: 1.15">
                </div>
                <div class="manual_description">' . get_static_option('site_manual_payment_description') . '</div>
            </div>';
            }

            $output .= renderAllPaymentGatewayExtraInfoBlade();
            $output .= '</div>';
        }

        return $output;
    }
    /**
     * Return all tenant gateways.
     */
    private static function getTenantGateways(): array
    {
        $gateways = PaymentGateway::where('status', StatusEnums::PUBLISH)
            ->get()
            ->map(function ($gateway) {
                $arr = $gateway->toArray();
                $arr['from_landlord'] = false;
                return $arr;
            })
            ->toArray();

        return $gateways;
    }

    /**
     * Return landlord gateways (used if payment_gateway_source = landlord_gateway).
     */
    private static function getLandlordGateways(CommissionSetting $setting): array
    {
        $gateways = CommissionSelectedGateway::where('commission_setting_id', $setting->id)
            ->where('status', 1)
            ->get()
            ->map(function ($gateway) {
                return [
                    'id' => $gateway->id,
                    'name' => $gateway->name,
                    'image' => $gateway->image,
                    'credentials' => !empty($gateway->credentials) ? json_decode($gateway->credentials, true) : null,
                    'test_mode' => $gateway->test_mode,
                    'from_landlord' => true,
                ];
            })
            ->toArray();
        return $gateways;
    }



}
