<?php

namespace EquipmentRental\Migrations;

use EquipmentRental\Models\RentalItem;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;

/**
 * Class CreateRentalItemTable
 */
class CreateRentalItemTable
{
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(RentalItem::class);
    }
}
