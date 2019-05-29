<?php

namespace EquipmentRental\Models;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

/**
 * Class RentalSetting
 *
 * @property string     $name
 * @property array  $value
 */
class RentalSetting extends Model
{
    public $id = 0;
    public $name        = '';
    public $value          = array();

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'EquipmentRental::RentalSetting';
    }

    public function save()
    {
        $database = pluginApp(DataBase::class);
        $database->save($this);
    }


}
