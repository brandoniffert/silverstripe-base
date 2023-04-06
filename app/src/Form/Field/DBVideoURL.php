<?php

namespace App\Form\Field;

use App\Util\Util;
use SilverStripe\ORM\FieldType\DBVarchar;

class DBVideoURL extends DBVarchar
{
    public function prepValueForDB($value)
    {
        if ($videoId = Util::getYouTubeID(trim($value))) {
            return sprintf('https://www.youtube.com/embed/%s', $videoId);
        }

        return $value;
    }

    public function ContentURL()
    {
        $value = $this->RAW();

        if ($videoId = Util::getYouTubeID(trim($value))) {
            return sprintf('https://www.youtube.com/watch/?v=%s', $videoId);
        }

        return $value;
    }
}
