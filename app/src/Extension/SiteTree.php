<?php

namespace App\Extension;

use App\Page\HomePage;
use SilverStripe\CMS\Model\RedirectorPage;
use SilverStripe\CMS\Model\VirtualPage;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\HTML;

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

    public function MetaTags(&$tags)
    {
        $siteConfig = SiteConfig::current_site_config();

        if ('' != $siteConfig->CanonicalDomain && VirtualPage::class != $this->owner->ClassName) {
            $canonicalBase = trim($siteConfig->CanonicalDomain, '/');

            if (method_exists($this->owner, 'CanonicalLink')) {
                $link = $this->owner->CanonicalLink();
            } else {
                $link = $this->owner->Link();
            }

            $canonLink = $canonicalBase . $link;

            $atts = [
                'rel' => 'canonical',
                'href' => $canonLink,
            ];

            $canonTag = HTML::createTag('link', $atts);

            $tagsArray = explode(PHP_EOL, $tags);
            $tagPattern = 'rel="canonical"';
            $tagSearch = function ($val) use ($tagPattern) {
                return false !== stripos($val, $tagPattern) ? true : false;
            };

            $currentTags = array_filter($tagsArray, $tagSearch);
            $cleanedTags = array_diff($tagsArray, $currentTags);
            $cleanedTags[] = $canonTag;

            $tags = implode(PHP_EOL, $cleanedTags);
        }
    }

    public function extraBodyClasses()
    {
        return [];
    }
}
