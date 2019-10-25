<?php

namespace Verleihliste\Contracts;

use Verleihliste\Models\RentalItem;

/**
 * Class RentalItemRepositoryContract
 * @package Verleihliste\Contracts
 */
interface RentalItemRepositoryContract
{
    /**
     * List all devices
     *
     * @return RentalItem[]
     */
    public function getDevices(): array;

    /**
     * Get a single device by deviceId
     *
     * @param int $deviceId
     * @return mixed
     */
    public function getDevice($deviceId);

    /**
     * Get a rented device per deviceId
     *
     * @param int $deviceId
     * @return Mixed
     */
    public function getRentedDevice($deviceId);

    /**
     * Delete a device from the rental list
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function deleteDevice($id,array $data);

    /**
     * Get the rent history of one device per id
     *
     * @return RentalItem[]
     */
    public function getDeviceHistory($id);

    /**
     * Get infos to rented devices
     *
     * @return RentalItem[]
     */
    public function getRentedDevices();
}
