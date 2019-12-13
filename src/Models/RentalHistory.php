<?php

namespace Verleihliste\Models;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;

/**
 * Class RentalHistory
 *
 * @property int     $id
 * @property int  $deviceId
 * @property RentalUser     $user
 * @property RentalUser     $adminUserId
 * @property string $comment
 * @property string $getBackComment
 * @property int $isAvailable
 * @property int $rent_until
 * @property int     $created_at
 * @property int     $status
 * @property string $name
 * @property int $getBackDate
 */
class RentalHistory extends Model
{
    /**
     * @var int
     */
    public $id              = 0;
    public $deviceId        = 0;
    public $user;
    public $adminUser;
    public $comment         = '';
    public $getBackComment  = '';
    public $isAvailable     = 0;
    public $rent_until      = 0;
    public $created_at      = 0;
    public $status          = 0;
    public $name            = '';
    public $getBackDate      = 0;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'Verleihliste::RentalHistory';
    }


}
