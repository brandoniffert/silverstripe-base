<?php

namespace App\Extension;

use App\Page\HomePage;
use SilverStripe\CMS\Model\RedirectorPage;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

class SiteTree extends DataExtension
{
    private static $db = [
        'ExtraBodyClass' => 'Varchar'
    ];

    public function IsHomePage()
    {
        return $this->owner->ClassName  === HomePage::class;
    }

    public function IsExternalRedirector()
    {
        if (RedirectorPage::class == $this->owner->ClassName) {
            return $this->owner->RedirectionType == 'External';
        }
    }

    public function getBodyClasses()
    {
        $classes = $this->owner->extraBodyClasses();

        $classes[] = $this->owner->ExtraBodyClass;
        $classes[] = sprintf('pagetype-%s', strtolower((new \ReflectionClass($this->owner))->getShortName()));
        $classes[] = sprintf('page-%s', strtolower($this->owner->URLSegment));

        $classes = array_filter($classes);

        return join(' ', $classes);
    }

    public function updateSettingsFields(FieldList $fields)
    {
        $fields->insertAfter(
            'Visibility',
            TextField::create('ExtraBodyClass', 'Extra CSS classes for body')
        );
    }

    public function MetaComponents(&$tags)
    {
        if (array_key_exists('description', $tags)) {
            unset($tags['description']);
        }
    }

    public function extraBodyClasses()
    {
        return [];
    }
}
