<?php

namespace App\Extension;

use App\Model\CallToAction;
use App\Model\MobileBarAction;
use App\Model\NavigationMenu;
use App\Model\SiteIcon;
use App\Model\ThirdPartyScript;
use App\Util\Util;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Director;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;

class SiteConfig extends DataExtension
{
    private static $db = [
        'GTMID' => 'Varchar',
        'CanonicalDomain' => 'Varchar(255)',
        'ContactAddress' => 'Varchar',
        'ContactCity' => 'Varchar',
        'ContactState' => 'Varchar',
        'ContactZip' => 'Varchar',
        'ContactPhoneNumber' => 'Varchar',
        'ContactEmail' => 'Varchar',
        'FacebookURL' => 'Varchar',
        'TwitterURL' => 'Varchar',
        'MobileBarEnabled' => 'Boolean',
        'MobileBarBgColor' => 'Varchar',
        'MobileBarFgColor' => 'Varchar'
    ];

    private static $has_one = [
        'SocialSharePhoto' => Image::class
    ];

    private static $has_many = [
        'MobileBarActions' => MobileBarAction::class . '.Owner',
        'ThirdPartyScripts' => ThirdPartyScript::class . '.Owner',
        'CallToActions' => CallToAction::class . '.Owner'
    ];

    private static $owns = [
        'SocialSharePhoto'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('Tagline');
        $fields->removeByName('CMSBrandingTab');

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('GTMID', 'Google Tag Manager ID'),
        ]);

        if (Security::getCurrentUser() && Security::getCurrentUser()->ID == 1) {
            $fields->addFieldsToTab('Root.Main', [
                GridField::create(
                    'ThirdPartyScripts',
                    'Third Party Scripts',
                    $this->owner->ThirdPartyScripts(),
                    Util::getRecordEditorConfig(false)
                )
            ]);
        }

        $fields->addFieldsToTab('Root.Contact Info', [
            TextField::create('ContactAddress', 'Street Address'),
            TextField::create('ContactCity', 'City'),
            TextField::create('ContactState', 'State'),
            TextField::create('ContactZip', 'Zip'),
            TextField::create('ContactPhoneNumber', 'Phone Number'),
            EmailField::create('ContactEmail', 'Email Address')
        ]);

        $fields->addFieldsToTab('Root.Social Media', [
            TextField::create('FacebookURL', 'Facebook URL'),
            TextField::create('TwitterURL', 'Twitter URL'),
            UploadField::create('SocialSharePhoto')
                ->setAllowedFileCategories('image')
        ]);

        // Icons tab
        $fields->addFieldsToTab('Root.Icons', [
            Util::cmsInfoMessage('Manages the icon library available throughout the site'),
            GridField::create(
                'Icons',
                'Icons',
                SiteIcon::get()->filter('IsCustom', true),
                Util::getRecordEditorConfig(false)
            )
        ]);

        // Menus tab
        $fields->addFieldsToTab('Root.Menus', [
            GridField::create(
                'Menus',
                'Menus',
                NavigationMenu::get(),
                Util::getRecordEditorConfig(false)
            )
        ]);

        $fields->addFieldsToTab('Root.Mobile Bar', [
            FieldGroup::create(
                'Enabled',
                CheckboxField::create('MobileBarEnabled', 'Enable mobile bar')
            ),
            ToggleCompositeField::create(
                'MobileBarStyles',
                'Style',
                [
                    TextField::create('MobileBarBgColor', 'Background color')
                        ->setDescription('As CSS hex value'),
                    TextField::create('MobileBarFgColor', 'Foreground color')
                        ->setDescription('As CSS hex value')
                ]
            ),
            GridField::create(
                'MobileBarActions',
                'Items',
                $this->owner->MobileBarActions(),
                Util::getRecordEditorConfig()
            )
        ]);

        $fields->addFieldsToTab(
            'Root.Canonical',
            LiteralField::create(
                'Info',
                '<p>The canonical domain will be added to the HTML head of your pages. It should be specified with the full protocol and with no trailing slash, eg. https://www.example.com</p>'
            ),
            TextField::create('CanonicalDomain')
                ->setDescription('eg. https://www.example.com')
        );
    }

    public function getSocialMediaList()
    {
        $items = [
            [
                'Name' => 'Facebook',
                'Icon' => 'facebook',
                'URL' => $this->owner->FacebookURL
            ],
            [
                'Name' => 'Twitter',
                'Icon' => 'twitter',
                'URL' => $this->owner->TwitterURL
            ]
        ];

        $list = ArrayList::create();

        foreach ($items as $item) {
            if ($item['URL']) {
                $list->push(ArrayData::create($item));
            }
        }

        return $list;
    }

    public function getDirectionsLink()
    {
        $address = [
            $this->owner->ContactAddress,
            $this->owner->ContactCity,
            $this->owner->ContactState,
            $this->owner->ContactZip
        ];

        $link = sprintf('https://www.google.com/maps/place/%s', join('+', $address));

        return str_replace(' ', '+', $link);
    }

    public function HasCTAs($key = null)
    {
        return $this->owner->CTAs($key)->count();
    }

    public function CTAs($key = null)
    {
        $ctas = $this->owner->CallToActions();

        if ($key) {
            $ctas = $ctas->filter('ActionSectionIdentifier', $key);
        }

        return $ctas;
    }

    public function getSiteStructuredData()
    {
        $data = [
            '@context' => 'http://schema.org',
            '@type' => 'Organization',
            'identifier' => $this->owner->CanonicalDomain,
            'name' => $this->owner->Title,
            'url' => $this->owner->CanonicalDomain,
            'logo' => $this->owner->SocialSharePhoto()->AbsoluteURL,
        ];

        return $data;
    }

    public function getMobileBar()
    {
        return ArrayData::create([
            'Enabled' => $this->owner->MobileBarEnabled,
            'Items' => $this->owner->MobileBarActions(),
            'BgColor' => $this->owner->MobileBarBgColor ?? '#000000',
            'FgColor' => $this->owner->MobileBarFgColor ?? '#ffffff'
        ]);
    }

    public function getHeadThirdPartyScripts()
    {
        $enabledScripts = [];

        $scripts = $this->owner->ThirdPartyScripts()->filter([
            'Enabled' => true,
            'Placement' => 'Head'
        ]);

        foreach ($scripts as $script) {
            if ($script->OnlyLive && !Director::isLive()) {
                continue;
            }

            $enabledScripts[] = $script;
        }

        return ArrayList::create($enabledScripts);
    }

    public function getBodyThirdPartyScripts()
    {
        $enabledScripts = [];

        $scripts = $this->owner->ThirdPartyScripts()->filter([
            'Enabled' => true,
            'Placement' => 'Body'
        ]);

        foreach ($scripts as $script) {
            if ($script->OnlyLive && !Director::isLive()) {
                continue;
            }

            $enabledScripts[] = $script;
        }

        return ArrayList::create($enabledScripts);
    }
}
