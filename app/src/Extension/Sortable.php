<?php

namespace App\Extension;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\Subsites\State\SubsiteState;

class Sortable extends DataExtension
{
    private static $default_sort = 'Sort ASC';

    private static $db = [
        'Sort' => 'Int'
    ];

    public function onBeforeWrite()
    {
        $shouldIgnore = $this->owner->config()->get('ignore_sortable_sort');
        $shouldSortBefore = $this->owner->config()->get('sortable_sort_before');

        if (!$this->owner->Sort && !$shouldIgnore) {
            $objs = DataObject::get($this->owner->ClassName);

            if ($this->owner->SubsiteID) {
                $objs = $objs->filter('SubsiteID', SubsiteState::singleton()->getSubsiteId());
            }

            if ($shouldSortBefore) {
                $this->owner->Sort = DataObject::get($this->owner->ClassName)->min('Sort') - 1;
            } else {
                $this->owner->Sort = $objs->max('Sort') + 1;
            }
        }

        parent::onBeforeWrite();
    }
}
