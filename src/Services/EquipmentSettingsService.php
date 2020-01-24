<?php
namespace Verleihliste\Services;

use Verleihliste\Helpers\LogHelper;
use Verleihliste\Models\RentalSetting;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

class EquipmentSettingsService
{
    /** @var DataBase $database */
    private $database;

    /** @var EquipmentRentalLogService $logService */
    private $logService;

    public function __construct(
        DataBase $database,
        EquipmentRentalLogService $logService
    )
    {
        $this->database = $database;
        $this->logService = $logService;
    }

    /**
     * Set a setting
     *
     * @param  string  $name
     * @param  string  $value
     * @return RentalSetting
     * @throws \Exception
     */
    public function setSetting($name,$value)
    {
        $getSetting = $this->getSettingObject($name);
        $setting = count($getSetting) == 0 ? pluginApp(RentalSetting::class) : $getSetting[0];
        $setting->name = $name;
        $setting->value = $value;

        $this->logService->addLog(0,LogHelper::DEVICE_CHANGED_SETTINGS_MESSAGE);

        return $this->database->save($setting);
    }

    /**
     * Get a setting
     *
     * @param string $name
     * @return string value
     */
    public function getSetting($name)
    {
        $setting = $this->database->query(RentalSetting::class)
            ->where("name","=",$name)
            ->limit(1)
            ->get();
        if(empty($setting))
            return null;
        return $setting[0]->value;
    }

    /**
     * Get a setting object
     *
     * @param string $name
     * @return null | RentalSetting
     */
    private function getSettingObject($name)
    {
        $setting = $this->database->query(RentalSetting::class)
            ->where("name","=",$name)
            ->limit(1)
            ->get();
        if(empty($setting))
            return null;
        return $setting;
    }

    /**
     * Get all settings
     *
     * @return RentalSetting[]
     */
    public function getSettings()
    {
        return $this->database->query(RentalSetting::class)->get();
    }
}
