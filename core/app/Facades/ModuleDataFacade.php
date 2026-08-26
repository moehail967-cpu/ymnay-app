<?php

namespace App\Facades;

use App\Helpers\ModuleMetaData;
use Illuminate\Support\Facades\Facade;


/**
 * @see ModuleMetaData
 * @method static getAllExternalPaymentGatewayMenu
 * @method static getExternalPaymentGateway
 * @method static getAllPaymentGatewayList
 * @method static renderAllPaymentGatewayExtraInfoBlade
 * @method static getAllPaymentGatewayListWithImage
 * @method static getAllTenantPaymentGatewayListWithImage
 * @method static renderPaymentGatewayImage($imageName, $moduleName)
 * @method static getPaymentGatewayImagePath($gateway_slug)
 * @method static getChargeCustomerMethodNameByPaymentGatewayName
 * @method static getChargeCustomerMethodNameByPaymentGatewayNameSpace
 * */
class ModuleDataFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ModuleDataFacade';
    }
}
