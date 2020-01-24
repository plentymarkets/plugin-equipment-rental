<?php


namespace Verleihliste\Services;

use Exception;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Modules\User\Contracts\UserRepositoryContract;
use Verleihliste\Models\RentalLog;

class EquipmentRentalLogService
{
    /** @var DataBase $database */
    private $database;

    /** @var UserRepositoryContract $userRepo */
    private $userRepo;

    public function __construct(
        DataBase $database,
        UserRepositoryContract $userRepo
    )
    {
        $this->database = $database;
        $this->userRepo = $userRepo;
    }

    public function addLog(int $rentalItem,string $message):RentalLog
    {
        try{
            $userId = $this->userRepo->getCurrentUser()->id;
        }
        catch(\Exception $e)
        {
            throw new Exception('Fehler beim Auslesen des Backend-Users'.$e->getMessage(), 400);
        }

        /* @var RentalLog $log */
        $log = pluginApp(RentalLog::class);
        $log->userId = $userId;
        $log->message = $message;
        $log->rentalItem = $rentalItem;
        $log->save();

        return $log;
    }

    /**
     *
     * Return log entries
     *
     * @return RentalLog[]
     */
    public function getLog()
    {
        return $this->database->query(RentalLog::class)->get();
    }
}
