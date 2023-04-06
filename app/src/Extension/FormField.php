<?php

namespace App\Extension;

use SilverStripe\Core\Extension;

class FormField extends Extension
{
    /**
     * Displays a helper message for using special emphasis characters.
     */
    public function showEmphasisHelper()
    {
        $currentTitle = $this->owner->RightTitle() ? trim($this->owner->RightTitle()) : '';
        $helper = 'Surround text in [ and ] brackets for emphasis styling.';

        // If there is already a RightTitle, append our message to the end
        if ($currentTitle) {
            if ('.' != substr($currentTitle, -1)) {
                $helper = sprintf('%s. %s', $currentTitle, $helper);
            } else {
                $helper = sprintf('%s %s', $currentTitle, $helper);
            }
        }

        $this->owner->setRightTitle($helper);

        return $this->owner;
    }

    /**
     * Displays a helper message for suggested size.
     */
    public function showSuggestedSizeHelper(string $sizeStr)
    {
        $currentTitle = $this->owner->RightTitle() ? trim($this->owner->RightTitle()) : '';
        $helper = sprintf('Suggested size: %s.', $sizeStr);

        // If there is already a RightTitle, append our message to the end
        if ($currentTitle) {
            if ('.' != substr($currentTitle, -1)) {
                $helper = sprintf('%s. %s', $currentTitle, $helper);
            } else {
                $helper = sprintf('%s %s', $currentTitle, $helper);
            }
        }

        $this->owner->setRightTitle($helper);

        return $this->owner;
    }

    /**
     * Displays a helper message for YouTube video url.
     */
    public function showYouTubeHelper(bool $hidePlaceholder = false)
    {
        $currentTitle = $this->owner->RightTitle() ? trim($this->owner->RightTitle()) : '';
        $helper = 'Enter a YouTube video URL (ex: https://www.youtube.com/watch?v=xxxxx).';

        // If there is already a RightTitle, append our message to the end
        if ($currentTitle) {
            if ('.' != substr($currentTitle, -1)) {
                $helper = sprintf('%s. %s', $currentTitle, $helper);
            } else {
                $helper = sprintf('%s %s', $currentTitle, $helper);
            }
        }

        if (!$hidePlaceholder) {
            $this->owner->setAttribute('placeholder', 'Video URL');
        }

        $this->owner->setRightTitle($helper);

        return $this->owner;
    }
}
