<?php

namespace App\Admin;

use App\Extension\FormSettings;
use SilverStripe\Core\ClassInfo;
use SilverStripe\ORM\DataObject;

trait AdminSettings
{
    public function getTitle()
    {
        if ($this->config()->get('settings_name')) {
            return $this->config()->get('settings_name');
        }

        $classParts = explode('\\', $this->ClassName);
        $className = array_pop($classParts);
        $className = str_replace('Settings', '', $className);
        $className = preg_replace('/\B[A-Z]/', ' $0', $className);

        return $className;
    }

    public static function current_settings()
    {
        $settings = DataObject::get_one(self::class);

        if ($settings) {
            return $settings;
        }

        return self::make_settings();
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        $settings = DataObject::get_one(self::class);

        if (!$settings) {
            self::make_settings();
        }
    }

    public static function make_settings()
    {
        // Get a unique ID for the new settings object
        // Even though all FormSettings are in different tables, they need
        // unique IDs to work inside FormAdmin
        $formSettingsIds = [];

        foreach (ClassInfo::subclassesFor(DataObject::class) as $className) {
            if (singleton($className)->hasExtension(FormSettings::class)) {
                if ($otherSettings = singleton($className)->get()->first()) {
                    $formSettingsIds[] = $otherSettings->ID;
                }
            }
        }

        $latestId = empty($formSettingsIds) ? 0 : max($formSettingsIds);

        $settings = self::create();
        $settings->ID = $latestId + 1;
        $settings->write(false, true, true); // Why do I need to do this?

        return $settings;
    }
}
