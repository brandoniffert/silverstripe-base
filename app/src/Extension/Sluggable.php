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

            $title = $this->owner->Title;

            $cleaned = str_replace(' ', '-', $title);
            $cleaned = str_replace('/', '-', $cleaned);
            $cleaned = preg_replace('/[^A-Za-z0-9\-]/', '', $cleaned);

            $urlSegment = $urlSegmentFilter->filter(trim($cleaned));

            $class = get_class($this->owner);
            $hasOne = $this->owner->hasOne();

            $filter = [
                'URLSegment' => $urlSegment
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

            $validUrlSegment = $urlSegment;

            $count = 2;
            while ($class::get()->filter($filter)->exclude('ID', $this->owner->ID)->exists()) {
                $validUrlSegment = $urlSegment . '-' . $count++;
                $filter['URLSegment'] = $validUrlSegment;
            }

            $this->owner->URLSegment = $validUrlSegment;
        }

        if ($this->owner->hasField('OverrideURLSegment')) {
            if ($this->owner->OverrideURLSegment) {
                $this->owner->URLSegment = $this->owner->OverrideURLSegment;
            }
        }
    }
}
