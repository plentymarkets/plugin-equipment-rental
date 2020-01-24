<?php

namespace Verleihliste\Repositories;

use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Verleihliste\Contracts\RentalItemRepositoryContract;
use Verleihliste\Helpers\LogHelper;
use Verleihliste\Models\RentalItem;
use Verleihliste\Services\EquipmentRentalLogService;
use Verleihliste\Validators\RentalItemValidator;


class RentalItemRepository implements RentalItemRepositoryContract
{
    /**
     * @var DataBase $database
     */
    private $database;

    /** @var EquipmentRentalLogService $logService */
    private $logService;

    public function __construct(
        DataBase $database,
        EquipmentRentalLogService $logService
    )
    {
        $this->database = $database;
        $this->logService = $logService;
    }

    /**
     * List all devices
     *
     * @return RentalItem[]
     */
    public function getDevices(): array
    {
        return $this->database->query(RentalItem::class)->get();
    }

    /**
     * Get a single device by deviceId
     *
     * @param int $deviceId
     * @return RentalItem|null
     */
    public function getDevice($deviceId)
    {
        /**
         * @var RentalItem $rentalItem
         */
        $rentalItem = $this->database->query(RentalItem::class)
            ->where('deviceId','=',$deviceId)
            ->orderBy('id','DESC')
            ->limit(1)
            ->get();

        return count($rentalItem) ? $rentalItem[0] : null;
    }

    /**
     * Get a rented device per deviceId
     *
     * @param int $deviceId
     * @return RentalItem|null
     */
    public function getRentedDevice($deviceId)
    {
        /**
         * @var RentalItem $rentalItem
         */
        $rentalItem = $this->database->query(RentalItem::class)
            ->where('deviceId','=',$deviceId)
            ->where('isAvailable', '=', 0)
            ->orderBy('id','DESC')
            ->limit(1)
            ->get();

        return count($rentalItem) ? $rentalItem[0] : null;
    }


    /**
     * Delete a device from the rental list
     *
     * @param  int  $id
     * @param  array  $data
     * @return RentalItem
     * @throws \Exception
     */
    public function deleteDevice($id,array $data)
    {
        /**
         * @var DataBase $database
         */
        $database = pluginApp(DataBase::class);

        $statusType = array(
            0 => 'Normal',
            1 => 'Defekt',
            2 => 'Entwendet',
            3 => 'Verloren',
        );

        $comment = !empty($data["comment"]) ? $data["comment"] : "";
        $status = !empty($data["status"]) && !empty($statusType[$data["status"]]) ? $data["status"] : 0;

        $rentItem = $database->query(RentalItem::class)
            ->where('deviceId', '=', $id)
            ->where('isAvailable', '=', 0)
            ->limit(1)
            ->get();

        if(!count($rentItem))
        {
            return;
        }
        $rentItem = $rentItem[0];
        $rentItem->isAvailable = 1;
        $rentItem->getBackDate = time();
        $rentItem->getBackComment = $comment;
        $rentItem->status = $status;
        $rentItem->save();

        $this->logService->addLog($rentItem->deviceId,LogHelper::DEVICE_GIVE_BACK_MESSAGE);

        return $rentItem;
    }

    /**
     * Get the rent history of one device per id
     *
     * @param int $id
     * @return RentalItem[]
     */
    public function getDeviceHistory($id)
    {
        /**
         * @var DataBase $database
         */
        $database = pluginApp(DataBase::class);

        $rentalItem = $database->query(RentalItem::class)
            ->where('deviceId', '=', $id)
            ->orderBy('id','DESC')
            ->get();
        return $rentalItem;
    }

    /**
     * Get infos to rented devices
     *
     * @return RentalItem[]
     */
    public function getRentedDevices()
    {
        /**
         * @var DataBase $database
         */
        $database = pluginApp(DataBase::class);
        $rentalItem = $database->query(RentalItem::class)
            ->where('isAvailable', '=', 0)
            ->orderBy('rent_until')
            ->get();
        return $rentalItem;
    }
}
