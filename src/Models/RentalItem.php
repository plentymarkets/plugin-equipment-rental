<?php

namespace EquipmentRental\Models;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

/**
 * Class RentalItem
 *
 * @property int     $id
 * @property int  $deviceId
 * @property int     $userId
 * @property int     $adminUserId
 * @property string $comment
 * @property string $getBackComment
 * @property int $isAvailable
 * @property int $rent_until
 * @property int     $created_at
 * @property int     $status
 */
class RentalItem extends Model
{
    /**
     * @var int
     */
    public $id              = 0;
    public $deviceId        = 0;
    public $userId          = 0;
    public $adminUserId     = 0;
    public $comment         = '';
    public $getBackComment  = '';
    public $isAvailable     = 0;
    public $rent_until      = 0;
    public $created_at      = 0;
    public $status          = 0;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'EquipmentRental::RentalItem';
    }

    public function save()
    {
        $database = pluginApp(DataBase::class);
        $database->save($this);
    }


}
