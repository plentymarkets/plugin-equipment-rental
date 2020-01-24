<?php

namespace Verleihliste\Models;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

/**
 * Class RentalLog
 *
 * @property int     $id
 * @property int    $userId
 * @property int    $rentalItem
 * @property string $message
 * @property int    $created_at
 */
class RentalLog extends Model
{
    /**
     * @var int
     */
    public $id              = 0;
    public $userId          = 0;
    public $rentalItem      = 0;
    public $message         = '';
    public $created_at      = 0;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'Verleihliste::RentalLog';
    }

    public function save()
    {
        $database = pluginApp(DataBase::class);
        $database->save($this);
    }
}
