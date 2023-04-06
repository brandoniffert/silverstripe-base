<?php

namespace App\Extension;

use App\Util\TextUtil;
use SilverStripe\Core\Extension;

class DBString extends Extension
{
    private static $casting = [
        'WrapSpecialCharacters' => 'HTMLFragment',
        'EmphasizeFromEnd' => 'HTMLFragment'
    ];

    public function WrapSpecialCharacters()
    {
        $patterns = [
            '/(\x{00AE})/u' => 'sup', // Registered Trademark symbol
        ];

        $value = $this->owner->RAW();

        if ($value) {
            foreach ($patterns as $pattern => $tag) {
                $value = preg_replace($pattern, '<' . $tag . '>${1}</' . $tag . '>', $value);
            }

            $value = TextUtil::emphasize($value);
        }

        return $value;
    }

    public function EmphasizeFromEnd()
    {
        $value = $this->owner->RAW();

        if ($value) {
            $value = TextUtil::emphasizeFromEnd($value);
        }

        return $value;
    }

    public function Deemphasize()
    {
        $value = $this->owner->RAW();

        if ($value) {
            $value = TextUtil::deemphasize($value);
        }

        return $value;
    }
}
