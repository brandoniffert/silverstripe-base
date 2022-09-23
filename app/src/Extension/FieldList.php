<?php

namespace App\Extension;

use SilverStripe\Core\Extension;

class FieldList extends Extension
{
    /**
     * Move a field to the Root.Main tab
     */
    public function moveFieldToMain(string $fieldName)
    {
        $field = $this->owner->moveFieldToTab($fieldName, 'Main');

        return $field;
    }

    /**
     * Move a field to a given tab
     */
    public function moveFieldToTab(string $fieldName, string $targetTabName)
    {
        $field = $this->owner->dataFieldByName($fieldName);
        $this->owner->removeByName($fieldName);
        $this->owner->addFieldToTab('Root.' . $targetTabName, $field);

        $tabSet = $this->owner->fieldByName('Root');

        // Always keep Element's Settings and History tab at the end
        if ($historyTab = $tabSet->FieldList()->fieldByName('History')) {
            $this->owner->removeByName('History');
            $tabSet->FieldList()->push($historyTab);
        }

        if ($settingsTab = $tabSet->FieldList()->fieldByName('Settings')) {
            $this->owner->removeByName('Settings');
            $tabSet->FieldList()->push($settingsTab);
        }

        return $field;
    }

    /**
     * Add a CSS class for visually hiding a field
     */
    public function hideField(string $fieldName)
    {
        $field = $this->owner->dataFieldByName($fieldName);

        $field->addExtraClass('hide-field');

        return $field;
    }

    public function fieldByNameCombine()
    {
        $parts = func_get_args();

        $name = join('-', $parts);

        return $this->owner->fieldByName($name);
    }
}
