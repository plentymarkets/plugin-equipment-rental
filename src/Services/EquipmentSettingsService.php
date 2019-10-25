<?php
namespace Verleihliste\Services;

use Verleihliste\Models\RentalSetting;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

class EquipmentSettingsService
{
    /** @var DataBase $database */
    private $database;

    public function __construct(DataBase $database)
    {
        $this->database = $database;
    }

    /**
     * Set a setting
     *
     * @param string $name
     * @param string $value
     * @return RentalSetting
     */
    public function setSetting($name,$value)
    {
        $getSetting = $this->getSettingObject($name);
        $setting = count($getSetting) == 0 ? pluginApp(RentalSetting::class) : $getSetting[0];
        $setting->name = $name;
        $setting->value = $value;
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
        $settings = $this->database->query(RentalSetting::class)->get();
        return $settings;
    }
}
