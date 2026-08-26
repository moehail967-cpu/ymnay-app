<?php

use App\Facades\ModuleDataFacade;
use Modules\PluginManage\Http\Helpers\PluginJsonFileHelper;

function getAllExternalPaymentGatewayMenu()
{
    return ModuleDataFacade::getAllExternalPaymentGatewayMenu();
}

function getExternalPaymentGateway()
{
    return ModuleDataFacade::getExternalPaymentGateway();
}

function getAllPaymentGatewayList()
{
    return ModuleDataFacade::getAllPaymentGatewayList();
}

function getAllPaymentGatewayListWithImage(bool $is_price_plan)
{
    return ModuleDataFacade::getAllPaymentGatewayListWithImage($is_price_plan);
}

function getAllTenantPaymentGatewayListWithImage($plan_based_payment_gateway)
{
    return ModuleDataFacade::getAllTenantPaymentGatewayListWithImage($plan_based_payment_gateway);
}

/**
 * @param $imageName
 * @param $moduleName
 * @return string
 */
function renderPaymentGatewayImage($imageName, $moduleName): string
{
    return ModuleDataFacade::renderPaymentGatewayImage($imageName, $moduleName);
}

function getPaymentGatewayImagePath($gateway_slug)
{
    return ModuleDataFacade::getPaymentGatewayImagePath($gateway_slug);
}

function renderAllPaymentGatewayExtraInfoBlade()
{
    return ModuleDataFacade::renderAllPaymentGatewayExtraInfoBlade();
}

/**
 * @param $payment_gateway_name
 * @return mixed
 */
function getChargeCustomerMethodNameByPaymentGatewayNameSpace($payment_gateway_name): mixed
{
    return ModuleDataFacade::getChargeCustomerMethodNameByPaymentGatewayNameSpace($payment_gateway_name);
}

/**
 * @param $payment_gateway_name
 * @return mixed
 */
function getChargeCustomerMethodNameByPaymentGatewayName($payment_gateway_name): mixed
{
    return ModuleDataFacade::getChargeCustomerMethodNameByPaymentGatewayName($payment_gateway_name);
}

function loadPaymentGatewayLogo($moduleName, $gatewayName)
{
    return route('payment.gateway.logo', [$moduleName, $gatewayName]);
}

function isPluginActive($moduleName)
{
    return (new PluginJsonFileHelper($moduleName))->isPluginActive();
}

function renderHeadStartHooks(): string
{
    ob_start();
    do_action('nazmart:frontend_head');
    return ob_get_clean();
}

function renderHeadEndHooks(): string
{
    ob_start();
    do_action('nazmart:frontend_head_end');
    return ob_get_clean();
}

function renderBodyStartHooks(): string
{
    ob_start();
    do_action('nazmart:frontend_body_start');
    return ob_get_clean();
}

function renderBodyEndHooks(): string
{
    ob_start();
    do_action('nazmart:frontend_footer');
    return ob_get_clean();
}

function renderNavbarIconsHooks(): string
{
    ob_start();
    do_action('nazmart:navbar_icons');
    return ob_get_clean();
}

function module_assets($moduleName, $imageName)
{
    return global_asset(module_dir($moduleName) . 'assets/' . $imageName);
}
