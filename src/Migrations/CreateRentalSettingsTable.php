<?php

namespace Verleihliste\Migrations;

use Verleihliste\Models\RentalSetting;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;

/**
 * Class CreateRentalSettingsTable
 */
class CreateRentalSettingsTable
{
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(RentalSetting::class);
    }
}
