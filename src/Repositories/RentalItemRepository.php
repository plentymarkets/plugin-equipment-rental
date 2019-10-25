<?php

namespace Verleihliste\Repositories;

use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Verleihliste\Contracts\RentalItemRepositoryContract;
use Verleihliste\Models\RentalItem;
use Verleihliste\Validators\RentalItemValidator;


class RentalItemRepository implements RentalItemRepositoryContract
{
    /**
     * List all devices
     *
     * @return RentalItem[]
     */
    public function getDevices(): array
    {
        $database = pluginApp(DataBase::class);

        /**
         * @var RentalItem[] $rentalItem
         */
        $rentalItem = $database->query(RentalItem::class)->get();
        return $rentalItem;
    }

    /**
     * Get a single device by deviceId
     *
     * @param int $deviceId
     * @return Mixed
     */
    public function getDevice($deviceId)
    {
        $database = pluginApp(DataBase::class);
        /**
         * @var RentalItem $rentalItem
         */
        $rentalItem = $database->query(RentalItem::class)
            ->where('deviceId','=',$deviceId)
            ->limit(1)
            ->orderBy('id','DESC')
            ->get();


        return count($rentalItem) != 0 ? $rentalItem[0] : null;
    }

    /**
     * Get a rented device per deviceId
     *
     * @param int $deviceId
     * @return Mixed
     */
    public function getRentedDevice($deviceId)
    {
        $database = pluginApp(DataBase::class);

        /**
         * @var RentalItem $rentalItem
         */
        $rentalItem = $database->query(RentalItem::class)
            ->where('deviceId','=',$deviceId)
            ->where('isAvailable', '=', 0)
            ->limit(1)
            ->orderBy('id','DESC')
            ->get();


        return count($rentalItem) != 0 ? $rentalItem[0] : null;
    }


    /**
     * Delete a device from the rental list
     *
     * @param int $id
     * @param array $data
     * @return RentalItem
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

        $rentalItem = $database->query(RentalItem::class)
            ->where('deviceId', '=', $id)
            ->where('isAvailable', '=', 0)
            ->limit(1)
            ->get();
        if(count($rentalItem) == 0)
        {
            return;
        }

        $rentItem = $rentalItem[0];

        $rentItem->isAvailable = 1;
        $rentItem->rent_until = time();
        $rentItem->getBackComment = $comment;
        $rentItem->status = $status;
        $rentItem->save();

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
