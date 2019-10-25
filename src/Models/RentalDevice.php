<?php

namespace Verleihliste\Models;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

/**
 * Class RentalDevice
 *
 * @property int     $id
 * @property string $name
 * @property int $isAvailable
 * @property string $image
 * @property mixed $attributes
 * @property mixed $properties
 * @property int $rent_until
 * @property string $created_at
 * @property mixed $status
 */
class RentalDevice extends Model
{
    /**
     * @var int
     */
    public $id              = 0;
    public $name            = '';
    public $isAvailable     = 0;
    public $image           = '';
    public $attributes      = '';
    public $properties      = '';
    public $created_at      = '';
    public $user;
    public $rent_until      = 0;
    public $status          = '';

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'Verleihliste::RentalDevice';
    }


}
