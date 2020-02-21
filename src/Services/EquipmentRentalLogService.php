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
        $log->created_at = time();
        $log->save();

        return $log;
    }

    /**
     * Return log entries
     *
     * @param int $page
     * @param int $itemsPerPage
     * @param array $filter
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $userId
     * @param int $variationId
     * @return array
     */
    public function getLog(int $page, int $itemsPerPage, array $filter, string $sortBy, string $sortOrder, int $userId, int $variationId)
    {
        $entries = $this->database->query(RentalLog::class)
            ->orderBy($sortBy,$sortOrder)
            ->forPage($page,$itemsPerPage);
        if($userId > 0){
            $entries = $entries->where('userId','=',$userId);
        }
        if($variationId > 0){
            $entries = $entries->where('rentalItem','=',$variationId);
        }
        $entries = $entries->get();
        $result['entries'] = $this->mapEntries($entries);
        $totalCount = $this->database->query(RentalLog::class)->getCountForPagination();
        $result = [
            'page' => $page,
            'isLastPage' => $page == ceil($totalCount/$itemsPerPage),
            'lastPageNumber' => ceil($totalCount/$itemsPerPage),
            'firstOnPage' => 1,
            'lastOnPage' => 2,
            'itemsPerPage' => $itemsPerPage,
            'totalsCount' => $totalCount,
            'entries' => $entries
        ];
        return $result;
    }

    private function mapEntries(array $entries): array
    {
        /** @var EquipmentRentalService $rentalService */
        $rentalService = pluginApp(EquipmentRentalService::class);
        $result = [];
        foreach ($entries as $rentalItem) {
            $rentalItem->rentalItem = $rentalService->getNameByVariationId($rentalItem->rentalItem);
            try{
                $user = $this->userRepo->getUserById($rentalItem->userId);
                $rentalItem->userId = $user->realName;
            }
            catch(Exception $e){
                $rentalItem->userId = 'UNKNOWN USER BY ID: '.$rentalItem->userId;
            }
            $result[] = $rentalItem;
        }
        return $result;
    }

}
