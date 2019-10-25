<?php

namespace Verleihliste\Models;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;

/**
 * Class RentalUser
 *
 * @property int     $id
 * @property string  $firstName
 * @property string     $lastName
 * @property string $email
 * @property int     $created_at
 */
class RentalUser extends Model
{
    /**
     * @var int
     */
    public $id              = 0;
    public $firstname        = '';
    public $lastname          = '';
    public $email      = '';
    public $created_at      = 0;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'Verleihliste::RentalUser';
    }
}
