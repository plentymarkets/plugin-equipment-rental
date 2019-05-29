<?php
namespace EquipmentRental\Services;

use Exception;
use EquipmentRental\Contracts\RentalItemRepositoryContract;
use EquipmentRental\Models\RentalHistory;
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Account\Contact\Models\Contact;
use Plenty\Modules\Item\Variation\Models\Variation;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use EquipmentRental\Models\RentalItem;
use EquipmentRental\Models\RentalUser;
use EquipmentRental\Validators\RentalItemValidator;
use EquipmentRental\Validators\RentalUserValidator;
use EquipmentRental\Validators\RentalMailValidator;
use Plenty\Modules\Account\Contact\Contracts\ContactRepositoryContract;
use Plenty\Modules\Account\Contact\Models\ContactType;
use Plenty\Modules\Account\Contact\Models\ContactOption;
use Plenty\Modules\User\Contracts\UserRepositoryContract;
use Plenty\Plugin\Http\Request;
use Plenty\Modules\Item\Variation\Contracts\VariationSearchRepositoryContract;
use Plenty\Repositories\Models\PaginatedResult;
use EquipmentRental\Models\RentalDevice;
use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Modules\Category\Contracts\CategoryRepositoryContract;
use Plenty\Modules\Item\ItemImage\Contracts\ItemImageSettingsRepositoryContract;
use Plenty\Plugin\Mail\Contracts\MailerContract;
use EquipmentRental\Services\EquipmentSettingsService;
use Plenty\Modules\Item\Variation\Contracts\VariationRepositoryContract;

class EquipmentRentalService
{
    /** @var RentalItemRepositoryContract */
    private $rentalItemRepo;

    /** @var ContactRepositoryContract */
    private $contactRepo;

    /** @var $userRepository */
    private $userRepository;

    /** @var $variationController */
    private $variationController;

    /** @var $itemImageSettingsRepo */
    private $itemImageSettingsRepo;

    /** @var $categoryRepo */
    private $categoryRepo;

    /** @var $settingsService */
    private $settingsService;

    public function __construct(RentalItemRepositoryContract $rentalItemRepo,
                                ContactRepositoryContract $contactRepo,
                                UserRepositoryContract $userRepository,
                                VariationSearchRepositoryContract $variationController,
                                ItemImageSettingsRepositoryContract $itemImageSettingsRepo,
                                CategoryRepositoryContract $categoryRepo,
                                EquipmentSettingsService $settingsService

    )
    {
        $this->rentalItemRepo = $rentalItemRepo;
        $this->contactRepo = $contactRepo;
        $this->userRepository = $userRepository;
        $this->variationController = $variationController;
        $this->itemImageSettingsRepo = $itemImageSettingsRepo;
        $this->categoryRepo = $categoryRepo;
        $this->settingsService = $settingsService;
    }

    /**
     * Rent a device
     *
     * @param array $data
     * @return RentalItem
     * @throws ValidationException
     */

    public function rentDevice(array $data): RentalItem
    {
        try {
            RentalItemValidator::validateOrFail($data);
            RentalUserValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        try{
            $authHelper = pluginApp(AuthHelper::class);
            $userRepository = $this->userRepository;
            $adminUserid = $authHelper->processUnguarded(
                function () use ($userRepository) {
                    $backendUser = $userRepository->getUserById(1);
                    return $backendUser->id;
                }
            );
        }
        catch(\Exception $e)
        {
            throw new Exception('Fehler beim Auslesen des Backend-Users'.$e->getMessage(), 400);
        }

        $selRentalDevice = $this->rentalItemRepo->getDevice($data["deviceId"]);

        if(!is_null($selRentalDevice) && $selRentalDevice->isAvailable == 0)
        {
            throw new Exception('Das Gerät ist bereits verliehen', 400);
        }

        if($data['rent_until'] > 0 && $data['rent_until'] < time())
        {
            throw new Exception('Das Datum liegt in der Vergangenheit', 400);
        }

        $contactId = $this->contactRepo->getContactIdByEmail($data["email"]);
        if(is_null($contactId))
        {
            try{
                $contact = $this->contactRepo->createContact(
                    [
                        "firstName" => $data["firstname"],
                        "lastName" => $data["lastname"],
                        "email" => $data["email"],
                        "typeId" => ContactType::TYPE_CUSTOMER,
                        "referrerId" => 1.00,
                        "options" => [
                            [
                                "typeId" => ContactOption::TYPE_MAIL,
                                "subTypeId" => ContactOption::SUBTYPE_PRIVATE,
                                "value" => $data["email"],
                                "priority" => 1
                            ]
                        ]
                    ]
                );
                $contactId = $contact->id;
            }
            catch (ValidationException $e) {
                throw $e;
            }
        }


        $rentalItem = pluginApp(RentalItem::class);
        $rentalItem->deviceId = $data['deviceId'];
        $rentalItem->userId = $contactId;
        $rentalItem->adminUserId = $adminUserid;
        $rentalItem->rent_until = $data['rent_until'];
        $rentalItem->created_at = time();
        $rentalItem->comment = !empty($data["comment"]) ? $data["comment"] : "";
        $rentalItem->isAvailable = 0;
        $rentalItem->save();

        return $rentalItem;
    }


    /**
     * Get a single device by deviceId
     *
     * @param int $deviceId
     * @return Mixed
     */
    public function getDevice($deviceId)
    {
        $device = $this->rentalItemRepo->getDevice($deviceId);
        if(is_null($device))
        {
            return;
        }
        $device = (array) $device;

        $device['user'] = $this->getUserDataById($device["userId"]);
        return $device;
    }

    /**
     * Get a single device by deviceId
     *
     * @param int $deviceId
     * @return Mixed
     */
    public function getRentedDevice($deviceId)
    {
        $device = $this->rentalItemRepo->getDevice($deviceId);
        if(is_null($device))
        {
            return;
        }
        $device = (array) $device;
        $device['user'] = $this->getUserDataById($device["userId"]);
        return $device;
    }

    /**
     * Get data of an user per id
     *
     * @param int $userId
     * @return RentalUser
     */
    private function getUserDataById($userId) : RentalUser
    {
        /** @var Contact $user */
        try
        {
            $user = $this->contactRepo->findContactById($userId);
        }
        catch(Exception $e)
        {
            return pluginApp(RentalUser::class);
        }
        $rentalUser = pluginApp(RentalUser::class);
        $rentalUser->id = $user->id;
        $rentalUser->firstname = $user->firstName;
        $rentalUser->lastname = $user->lastName;
        $rentalUser->email = $user->email;
        return $rentalUser;
    }

    /**
     * Get data of an admin user per id
     *
     * @param int $userId
     * @return RentalUser
     */
    private function getAdminUserDataById($userId) : RentalUser
    {
        /** @var Contact $user */
        try
        {
            $user = $this->contactRepo->findContactById($userId);
        }
        catch(Exception $e)
        {
            return pluginApp(RentalUser::class);
        }

        $rentalUser = pluginApp(RentalUser::class);
        $rentalUser->id = $user->id;
        $rentalUser->firstname = $user->firstName;
        $rentalUser->lastname = $user->lastName;
        $rentalUser->email = $user->email;
        return $rentalUser;
    }

    /**
     * Get all articles with device informations
     *
     * @param Request $request
     * @return array
     * @throws /Exception
     */
    public function getDevices(Request $request)
    {
        $withString = "itemImages,item,variationAttributeValues,properties";
        $with = array_flip(explode(',', $withString));
        $this->variationController->setSearchParams(['with' => $with]);
        $this->variationController->setSearchFilter("variationCategory.hasCategory",["categoryId" => $request->get("categoryId",'')]);

        /** @var PaginatedResult $result */
        $result = $this->variationController->search()->toArray();

        if(is_null($result)){
            throw new \Exception('Fehler beim Auslesen der Artikel', 400);
        }

        $variations=[];
        foreach($result["entries"] as $variation)
        {
            $device = $this->getDevice($variation["id"]);
            $user = !is_null($device) && !$device["isAvailable"] ? $this->getUserDataById($device["userId"]) : "";

            $categoryInfo = $this->categoryRepo->get($request->get("categoryId",''));
            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            if(!is_null($categoryInfo) && !empty($categoryInfo->details[0]->imagePath)) {
                $defaultImage = sprintf("%s/documents/%s",$actual_link,$categoryInfo->details[0]->imagePath);
            }
            else{
                $imageSettings = $this->itemImageSettingsRepo->get();
                $defaultImage = $actual_link.$imageSettings->placeholder["imagePlaceholderURL"];
            }
            $defaultImage = str_replace("master.login","master",$defaultImage); //test


            $rentalDevice = pluginApp(RentalDevice::class);
            $rentalDevice->id = $variation["id"];
            $rentalDevice->name = $variation["name"];
            $rentalDevice->image = !empty($variation["itemImages"]) ? $variation["itemImages"][0]["url"]: $defaultImage;  //$variation->image
            $rentalDevice->isAvailable = !is_null($device) ? $device["isAvailable"] : 1;
            $rentalDevice->attributes = $variation["variationAttributeValues"];
            $rentalDevice->properties = $variation["properties"];
            $rentalDevice->user = !empty($user) ? sprintf("%s %s",ucfirst($user->firstname),ucfirst($user->lastname)) : "";
            $rentalDevice->created_at = $variation["created_at"];
            array_push($variations,$rentalDevice);
        }
        return $variations;
    }

    /**
     * Get the rent history of one device per id
     *
     * @param int $deviceId
     * @return Mixed
     */
    public function getDeviceHistory($deviceId)
    {
        $devices = $this->rentalItemRepo->getDeviceHistory($deviceId);
        $history = [];
        foreach($devices as $device)
        {
            $historyDevice = pluginApp(RentalHistory::class);
            $historyDevice->id = $device->id;
            $historyDevice->deviceId = $device->deviceId;
            $historyDevice->user = $this->getUserDataById($device->userId);
            $historyDevice->adminUser = $this->getAdminUserDataById($device->adminUserId);
            $historyDevice->comment = $device->comment;
            $historyDevice->getBackComment = $device->getBackComment;
            $historyDevice->isAvailable = $device->isAvailable;
            $historyDevice->rent_until = $device->rent_until;
            $historyDevice->created_at = $device->created_at;
            $historyDevice->status = $device->status;

            array_push($history,$historyDevice);
        }
        return $history;
    }

    /**
     * Get infos to rented devices ordered by rent_until
     *
     * @return Mixed
     */
    public function getRentedDevices()
    {
        $devices = $this->rentalItemRepo->getRentedDevices();
        $rentedDevices = [];
        $rentedUndefinedTime = [];
        foreach($devices as $device)
        {
            $rentedDevice = pluginApp(RentalHistory::class);
            $rentedDevice->id = $device->id;
            $rentedDevice->deviceId = $device->deviceId;
            $rentedDevice->user = $this->getUserDataById($device->userId);
            $rentedDevice->adminUser = $this->getAdminUserDataById($device->adminUserId);
            $rentedDevice->comment = $device->comment;
            $rentedDevice->getBackComment = $device->getBackComment;
            $rentedDevice->isAvailable = $device->isAvailable;
            $rentedDevice->rent_until = $device->rent_until;
            $rentedDevice->created_at = $device->created_at;
            $rentedDevice->status = $device->status;

            if($rentedDevice->rent_until == 0)
                array_push($rentedUndefinedTime,$rentedDevice);
            else
                array_push($rentedDevices,$rentedDevice);
        }
        return array_merge($rentedDevices,$rentedUndefinedTime);
    }

    /**
     * Get name per variation id
     *
     * @param int $id
     * @return string
     */
    private function getNameByVariationId($id)
    {
        $name = "NONAME";

        $variationRepository = pluginApp(VariationRepositoryContract::class);
        /** @var Variation $result */
        $result = $variationRepository->show($id,[],"de");
        if(!is_null($result))
        {
            $result = (array) $result;
            if(!empty($result["name"]))
                $name = $result["name"];
        }

        return $name;
    }

    /**
     * Replace E-mail placefolders
     *
     * @param string $template
     * @param RentalUser $user
     * @param array $device
     * @return string
     */
    private function replaceMailPlaceholders($template,RentalUser $user,$device)
    {
        $placeholders = [
            '$deviceName' => $this->getNameByVariationId($device["deviceId"]),
            '$deviceRentedAt' => date("d.m.Y",$device["rent_until"]),
            '$deviceOwnerFirstName' => ucfirst($user->firstname),
            '$deviceOwnerLasatName' => ucfirst($user->lastname),
            '$deviceOwnerFullName' => ucfirst($user->firstname)." ".ucfirst($user->lastname)
        ];
        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }

    /**
     * Send reminder mail to user
     *
     * @param array $data
     * @throws Exception,ValidationException
     * @return Mixed
     */
    public function reminderMail(array $data)
    {
        try {
            RentalMailValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        $userId = $data["userId"];
        $deviceId = $data["deviceId"];

        $device = $this->getRentedDevice($deviceId);
        if(is_null($device))
            throw new Exception("Das Gerät wurde nicht gefunden",400);

        $user = $this->getUserDataById($userId);
        if($user->id == 0)
            throw new Exception("Es wurde kein Benutzer mit dieser ID gefunden",400);

        $emailTemplate = $this->settingsService->getSetting("emailTemplate");
        if(is_null($emailTemplate))
            throw new Exception("Es wurde kein E-Mail Template in den Einstellungen festgelegt.",400);

        $emailTemplateTopic = $this->settingsService->getSetting("emailTemplateTopic");
        if(is_null($emailTemplateTopic))
            throw new Exception("Es wurde kein E-Mail Betreff in den Einstellungen festgelegt.",400);


        $emailHtml = $this->replaceMailPlaceholders($emailTemplate,$user,$device);
        $mailer = pluginApp(MailerContract::class);
        return $mailer->sendHtml($emailHtml,[
            $user->email
        ],$emailTemplateTopic);
    }
}
