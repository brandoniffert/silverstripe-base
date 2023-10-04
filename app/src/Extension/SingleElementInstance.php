<?php

namespace App\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\DataObject;

class SingleElementInstance extends Extension
{
    public function canCreate($member = null, $context = [])
    {
        $existing = DataObject::get_one($this->owner->ClassName);

        return !$existing;
    }
}
