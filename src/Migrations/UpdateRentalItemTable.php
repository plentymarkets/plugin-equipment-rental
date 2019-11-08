<?php

namespace Verleihliste\Migrations;

use Verleihliste\Models\RentalItem;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;

/**
 * Class UpdateRentalItemTable
 */
class UpdateRentalItemTable
{
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->updateTable(RentalItem::class);
    }
}
