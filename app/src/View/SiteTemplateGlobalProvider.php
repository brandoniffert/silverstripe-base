<?php

namespace App\View;

use App\Model\NavigationMenu;
use App\Util\TextUtil;
use App\Util\Util;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Environment;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;
use SilverStripe\View\TemplateGlobalProvider;

class SiteTemplateGlobalProvider implements TemplateGlobalProvider
{
    public static function get_template_global_variables()
    {
        return [
            'NavigationMenu' => [
                'method' => 'NavigationMenu'
            ],
            'NavigationMenuCacheKey' => [
                'method' => 'NavigationMenuCacheKey'
            ],
            'PhoneLink' => 'PhoneLink',
            'PhoneLinker' => [
                'method' => 'PhoneLinker',
                'casting' => 'HTMLFragment',
            ],
            'GetPageByType' => 'GetPageByType',
            'TextEmphasize' => [
                'method' => 'TextEmphasize',
                'casting' => 'HTMLFragment',
            ],
            'TextDeemphasize' => [
                'method' => 'TextDeemphasize',
                'casting' => 'HTMLFragment',
            ],
            'TextEmphasizeFromStart' => [
                'method' => 'TextEmphasizeFromStart',
                'casting' => 'HTMLFragment',
            ],
            'TextEmphasizeFromEnd' => [
                'method' => 'TextEmphasizeFromEnd',
                'casting' => 'HTMLFragment',
            ],
            'Looper' => 'Looper',
            'DontCache' => 'DontCache'
        ];
    }

    public static function NavigationMenu($key)
    {
        if ($menu = NavigationMenu::get()->filter('Key', $key)->first()) {
            $pages = $menu->Pages()->filter([
                'ShowInMenus' => true
            ])->sort('PageSort');

            if ($pages->count()) {
                return $pages;
            }
        }
    }

    public static function NavigationMenuCacheKey($key)
    {
        if ($menu = NavigationMenu::get()->filter('Key', $key)->first()) {
            $pages = $menu->Pages()->filter([
                'ShowInMenus' => true
            ])->sort('PageSort');

            $siteConfig = SiteConfig::current_site_config();

            $parts = [
                "navigation-menu-{$key}",
                SiteTree::get()->max('LastEdited'),
                $menu->LastEdited,
                $siteConfig->LastEdited,
                join('-', $pages->column('ID')),
                join('-', $pages->relation('Children')->column('ID'))
            ];

            return implode('-_-', $parts);
        }
    }

    public static function PhoneLink($number, $extension = '')
    {
        $cleanNumber = Util::cleanPhoneNumber($number);

        if (!is_null($extension) && $extension != '') {
            return sprintf('tel:+1%sx%s', $cleanNumber, $extension);
        }

        return sprintf('tel:+1%s', $cleanNumber);
    }

    public static function PhoneLinker($number, $classes = '', $text = null)
    {
        $text = is_null($text) ? $number : $text;
        $cleanedNumber = sprintf('+1%s', Util::cleanPhoneNumber($number));

        return sprintf('<a href="tel:%s" class="phone-link %s">%s</a>', $cleanedNumber, $classes, $text);
    }

    public static function GetPageByType($pageType)
    {
        return DataObject::get_one('App\\Page\\' . $pageType);
    }

    public static function TextEmphasize($text)
    {
        return TextUtil::emphasize($text);
    }

    public static function TextDeemphasize($text)
    {
        return TextUtil::deemphasize($text);
    }

    public static function TextEmphasizeFromStart($text, $numWords = 1)
    {
        return TextUtil::emphasizeFromStart($text, $numWords);
    }

    public static function TextEmphasizeFromEnd($text, $numWords = 1)
    {
        return TextUtil::emphasizeFromEnd($text, $numWords);
    }

    public static function Looper($count)
    {
        return ArrayList::create(array_fill(0, (int) $count, ArrayData::create()));
    }

    public static function DontCache()
    {
        return Environment::getEnv('SS_DISABLE_PARTIAL_CACHING');
    }
}
