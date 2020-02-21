<?php

namespace Verleihliste\Providers;


use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;

class EquipmentRentalRouteServiceProvider extends RouteServiceProvider
{
    public function map(Router $router)
    {
        $router->get('plugin/equipmentRental/rentalDevice','Verleihliste\Controllers\ContentController@getDevices');
        $router->get('plugin/equipmentRental/rentalDeviceById','Verleihliste\Controllers\ContentController@getDeviceById');
        $router->get('plugin/equipmentRental/rentalDevice/{id}','Verleihliste\Controllers\ContentController@getDevice')->where('id', '\d+');
        $router->get('plugin/equipmentRental/rentalDevice/{id}','Verleihliste\Controllers\ContentController@getRentedDevice')->where('id', '\d+');
        $router->post('plugin/equipmentRental/rentalDevice','Verleihliste\Controllers\ContentController@rentDevice');
        $router->put('plugin/equipmentRental/rentalDevice/{id}','Verleihliste\Controllers\ContentController@deleteDevice')->where('id', '\d+');
        $router->get('plugin/equipmentRental/rentalDevice/history/{id}','Verleihliste\Controllers\ContentController@getDeviceHistory')->where('id', '\d+');
        $router->get('plugin/equipmentRental/rentalDevice/getRentedDevices','Verleihliste\Controllers\ContentController@getRentedDevices');
        $router->put('plugin/equipmentRental/rentalDevice/remindEmail','Verleihliste\Controllers\ContentController@remindEmail');

        $router->put('plugin/equipmentRental/rentalDevice/setting','Verleihliste\Controllers\ContentController@setSetting');
        $router->get('plugin/equipmentRental/rentalDevice/setting','Verleihliste\Controllers\ContentController@getSetting');
        $router->get('plugin/equipmentRental/rentalDevice/settings','Verleihliste\Controllers\ContentController@getSettings');

        $router->get('plugin/equipmentRental/rentalDevice/findUser/{name}','Verleihliste\Controllers\ContentController@findUser');

        $router->post('plugin/equipmentRental/createItem','Verleihliste\Controllers\ContentController@createItem');
        $router->get('plugin/equipmentRental/log','Verleihliste\Controllers\ContentController@log');
    }

}
