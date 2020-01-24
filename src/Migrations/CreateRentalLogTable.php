<?php

namespace Verleihliste\Migrations;

use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Verleihliste\Models\RentalLog;

/**
 * Class CreateRentalLogTable
 */
class CreateRentalLogTable
{
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(RentalLog::class);
    }
}
