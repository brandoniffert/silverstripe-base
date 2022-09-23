<?php

namespace App\Extension;

use SilverStripe\ORM\DataExtension;
use SilverStripe\View\Parsers\URLSegmentFilter;

class Sluggable extends DataExtension
{
    private static $db = [
        'URLSegment' => 'Varchar'
    ];

    private static $indexes = [
        'URLSegment' => true
    ];

    public function onBeforeWrite()
    {
        if (!$this->owner->URLSegment || ($this->owner->URLSegment && $this->owner->isChanged('Title'))) {
            $urlSegmentFilter = URLSegmentFilter::create();
            $urlSegmentFilter->setAllowMultibyte(true);
            $this->owner->URLSegment = $urlSegmentFilter->filter(trim($this->owner->Title));


            $class = get_class($this->owner);
            $hasOne = $this->owner->hasOne();

            $filter = [
                'URLSegment' => $this->owner->URLSegment,
            ];

            if (isset($hasOne['Parent'])) {
                $filter['ParentID'] = $this->owner->ParentID;
            }

            if (isset($hasOne['Owner'])) {
                $filter['OwnerID'] = $this->owner->OwnerID;
            }

            if ($customUniqueFields = $this->owner->config()->get('sluggable_custom_unique_fields')) {
                foreach ($customUniqueFields as $field) {
                    if ($this->owner->hasField($field)) {
                        $filter[$field] = $this->owner->getField($field);
                    }
                }
            }

            $count = 1;
            while ($class::get()->filter($filter)->exclude('ID', $this->owner->ID)->exists()) {
                $this->owner->URLSegment = $this->owner->URLSegment . '-' . $count++;
                $filter['URLSegment'] = $this->owner->URLSegment;
            }
        }
    }
}
