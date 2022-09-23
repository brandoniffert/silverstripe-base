<?php

namespace App\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBField;

class Image extends Extension
{
    public function WithClasses($extraClasses = '')
    {
        $existingClasses = $this->owner->getAttribute('class');
        $classes = is_null($existingClasses) ? trim($extraClasses) : "{$existingClasses} {$extraClasses}";

        return $this->owner->setAttribute('class', $classes);
    }

    public function SrcSet($requestedWidth = null, $requestedHeight = null)
    {
        // Don't try this on svg images
        if (strpos($this->owner->FileFilename, '.svg') != false) {
            return false;
        }

        if (!$requestedWidth || !$requestedHeight) {
            $requestedWidth = $this->owner->Width;
            $requestedHeight = $this->owner->Height;
        }

        $aspectRatio = $requestedHeight / $requestedWidth;

        $generatedWidths = [
            2048,
            1440,
            1260,
            1080,
            620,
            320
        ];

        $srcset = [];

        foreach ($generatedWidths as $generatedWidth) {
            // Don't upscale if the original image isn't big enough and account for 2x size if specifying a width
            if ($this->owner->Width >= $generatedWidth && ($requestedWidth * 2) >= $generatedWidth) {
                $source = $this->getImageSource($generatedWidth, $aspectRatio);
                $srcset[] = sprintf("%s %sw", $source, $generatedWidth);
            }
        }

        return DBField::create_field(
            'HTMLText',
            sprintf('srcset="%s"', join(', ', $srcset))
        );
    }

    private function getImageSource($targetWidth = null, $aspectRatio = 1)
    {
        $targetWidth = $targetWidth >= $this->owner->Width ? $this->owner->Width : $targetWidth;
        $targetHeight = intval($targetWidth * $aspectRatio);

        return $this->owner->FocusFill($targetWidth, $targetHeight)->AbsoluteURL;
    }
}
