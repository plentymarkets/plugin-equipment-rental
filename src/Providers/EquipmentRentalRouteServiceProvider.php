<?php

namespace EquipmentRental\Providers;


use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;

class EquipmentRentalRouteServiceProvider extends RouteServiceProvider
{
    public function map(Router $router)
    {
        $router->get('plugin/equipmentRental/rentalDevice','EquipmentRental\Controllers\ContentController@getDevices');
        $router->get('plugin/equipmentRental/rentalDeviceById','EquipmentRental\Controllers\ContentController@getDeviceById');
        $router->get('plugin/equipmentRental/rentalDevice/{id}','EquipmentRental\Controllers\ContentController@getDevice')->where('id', '\d+');
        $router->get('plugin/equipmentRental/rentalDevice/{id}','EquipmentRental\Controllers\ContentController@getRentedDevice')->where('id', '\d+');
        $router->post('plugin/equipmentRental/rentalDevice','EquipmentRental\Controllers\ContentController@rentDevice');
        $router->put('plugin/equipmentRental/rentalDevice/{id}','EquipmentRental\Controllers\ContentController@deleteDevice')->where('id', '\d+');
        $router->get('plugin/equipmentRental/rentalDevice/history/{id}','EquipmentRental\Controllers\ContentController@getDeviceHistory')->where('id', '\d+');
        $router->get('plugin/equipmentRental/rentalDevice/getRentedDevices','EquipmentRental\Controllers\ContentController@getRentedDevices');
        $router->put('plugin/equipmentRental/rentalDevice/remindEmail','EquipmentRental\Controllers\ContentController@remindEmail');

        $router->put('plugin/equipmentRental/rentalDevice/setting','EquipmentRental\Controllers\ContentController@setSetting');
        $router->get('plugin/equipmentRental/rentalDevice/setting','EquipmentRental\Controllers\ContentController@getSetting');
        $router->get('plugin/equipmentRental/rentalDevice/settings','EquipmentRental\Controllers\ContentController@getSettings');

        $router->get('plugin/equipmentRental/rentalDevice/findUser/{name}','EquipmentRental\Controllers\ContentController@findUser');

        $router->post('plugin/equipmentRental/createItem','EquipmentRental\Controllers\ContentController@createItem');


    }

}
