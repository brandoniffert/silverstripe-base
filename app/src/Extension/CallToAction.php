<?php

namespace App\Extension;

use App\Util\Util;
use SilverStripe\CMS\Model\VirtualPage;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;

class CallToAction extends DataExtension
{
    private static $cascade_deletes = [
        'CallToActions'
    ];

    private static $has_many = [
        'CallToActions' => 'App\Model\CallToAction.Owner'
    ];

    private static $owns = [
        'CallToActions'
    ];

    public function HasCTAs($key = null)
    {
        return $this->owner->CTAs($key)->count();
    }

    public function CTAs($key = null)
    {
        $owner = VirtualPage::class == $this->owner->ClassName ?
            $this->owner->CopyContentFrom() :
            $this->owner;

        $ctas = $owner->CallToActions();

        if ($key) {
            $ctas = $ctas->filter('ActionSectionIdentifier', $key);
        }

        $validCtas = ArrayList::create([]);

        foreach ($ctas as $cta) {
            if ($cta->ActionType == 'Element') {
                if ($cta->ActionElement()->exists() && !$cta->ActionElement()->IsHidden) {
                    $validCtas->push($cta);
                }
            } else {
                $validCtas->push($cta);
            }
        }

        return $validCtas;
    }

    // Helper for building a CTA Grid Field component
    public function buildCTAGrid($key = null)
    {
        $gridKey = 'CallToActions';
        $ctas = $this->owner->CallToActions();

        if ($key) {
            $ctas = $ctas->filter('ActionSectionIdentifier', $key);
            $gridKey = $key;
        }

        return GridField::create(
            $gridKey,
            'Call To Actions',
            $ctas,
            Util::getRecordEditorConfig()
        );
    }
}
