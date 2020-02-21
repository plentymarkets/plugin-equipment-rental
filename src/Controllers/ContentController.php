<?php

namespace Verleihliste\Controllers;


use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Verleihliste\Contracts\RentalItemRepositoryContract;
use Verleihliste\Models\RentalItem;
use Verleihliste\Models\RentalLog;
use Verleihliste\Services\EquipmentRentalLogService;
use Verleihliste\Services\EquipmentRentalService;
use Verleihliste\Services\EquipmentSettingsService;
use Exception;

class ContentController extends Controller
{
    /**
     * @param Request $request
     * @param EquipmentRentalService $rentalService
     * @throws /Exception
     * @return string
     */
    public function getDevices(Request $request,EquipmentRentalService $rentalService): string
    {
        $devices = $rentalService->getDevices($request);
        return json_encode($devices);
    }

    /**
     * @param Request $request
     * @param EquipmentRentalService $rentalService
     * @throws /Exception
     * @return string
     */
    public function getDeviceById(Request $request,EquipmentRentalService $rentalService): string
    {
        $devices = $rentalService->getDeviceById($request);
        return json_encode($devices);
    }

    /**
     * @param int $deviceId
     * @param EquipmentRentalService $rentalService
     * @return string
     */
    public function getDevice(int $deviceId, EquipmentRentalService $rentalService): string
    {
        $device = $rentalService->getDevice($deviceId);
        return !is_null($device) ? json_encode($device) : "";
    }

    /**
     * @param int $deviceId
     * @param EquipmentRentalService $rentalService
     * @return string
     */
    public function getRentedDevice(int $deviceId, EquipmentRentalService $rentalService): string
    {
        $device = $rentalService->getRentedDevice($deviceId);
        return !is_null($device) ? json_encode($device) : "";
    }

    /**
     * @param  Request $request
     * @param EquipmentRentalService $rentalService
     * @throws  Exception
     * @return string
     */
    public function rentDevice(Request $request, EquipmentRentalService $rentalService): string
    {
        $rentItem = $rentalService->rentDevice($request->all());
        return json_encode($rentItem);
    }

    /**
     * @param int                    $id
     * @param Request   $request
     * @param RentalItemRepositoryContract  $rentalItemRepo
     * @return string
     */
    public function deleteDevice(int $id, Request $request, RentalItemRepositoryContract $rentalItemRepo): string
    {
        $deleteDevice = $rentalItemRepo->deleteDevice($id,$request->all());
        return json_encode($deleteDevice);
    }

    /**
     * @param int $id
     * @param EquipmentRentalService $rentalService
     * @return string
     */
    public function getDeviceHistory(int $id,EquipmentRentalService $rentalService): string
    {
        $history = $rentalService->getDeviceHistory($id);
        return json_encode($history);
    }

    /**
     * @param EquipmentRentalService $rentalService
     * @return string
     */
    public function getRentedDevices(EquipmentRentalService $rentalService): string
    {
        $rentedDevices = $rentalService->getRentedDevices();
        return json_encode($rentedDevices);
    }

    /**
     * @param EquipmentRentalService $rentalService
     * @param Request $request
     * @throws Exception
     * @return string
     */
    public function remindEmail(Request $request, EquipmentRentalService $rentalService): string
    {
        $sendMail = $rentalService->reminderMail($request->all());
        return json_encode($sendMail);
    }

    /**
     * Set a setting
     *
     * @param Request $request
     * @param EquipmentSettingsService $settingsService
     * @throws Exception
     * @return string
     */
    public function setSetting(Request $request,EquipmentSettingsService $settingsService)
    {
        if(empty($request->get("name"))){
            throw new Exception("Es wurde kein Name angegeben",400);
        }
        if(empty($request->get("value"))){
            throw new Exception("Es wurde keine Value angegeben",400);
        }
        $setting = $settingsService->setSetting($request->get("name"),$request->get("value"));
        return json_encode($setting);
    }

    /**
     * Get a setting
     *
     * @param Request $request
     * @param EquipmentSettingsService $settingsService
     * @throws Exception
     * @return string
     */
    public function getSetting(Request $request,EquipmentSettingsService $settingsService)
    {
        if(empty($request->get("name"))){
            throw new Exception("Es wurde kein Name angegeben",400);
        }
        $setting = $settingsService->getSetting($request->get("name"));
        return json_encode($setting);
    }

    /**
     * Get all settings
     *
     * @param EquipmentSettingsService $settingsService
     * @return string
     */
    public function getSettings(EquipmentSettingsService $settingsService)
    {
        $setting = $settingsService->getSettings();
        return json_encode($setting);
    }


    /**
     * Find an user by name
     *
     * @param string $name
     * @param EquipmentRentalService $rentalService
     * @return string
     */
    public function findUser($name,EquipmentRentalService $rentalService)
    {
        $users = $rentalService->findUser($name);
        return json_encode($users);
    }

    /**
     * @param Request $request
     * @param EquipmentRentalService $rentalService
     * @throws /Exception
     * @return string
     */
    public function createItem(Request $request,EquipmentRentalService $rentalService): string
    {
        $device = $rentalService->createItem($request);
        return json_encode($device);
    }

    /**
     * List log entries
     *
     * @param Request $request
     * @param EquipmentRentalLogService $logService
     * @return string
     */
    public function log(Request $request, EquipmentRentalLogService $logService): string
    {
        $page = $request->get('page',1);
        $itemsPerPage = $request->get('itemsPerPage',25);
        $sortBy = $request->get("sortBy", 'id');
        $sortOrder = $request->get("sortOrder", 'desc');
        $userId = $request->get('user',0);
        $variationId = $request->get('device',0);
        $filter = [
            'name' => $request->get("name")
        ];
        return json_encode($logService->getLog($page, $itemsPerPage, $filter, $sortBy, $sortOrder,$userId, $variationId));
    }

}
