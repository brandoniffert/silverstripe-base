<?php

namespace App\Extension;

use App\Model\NavigationMenu;
use App\Model\SiteIcon;
use App\Util\Util;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
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
        'TwitterURL' => 'Varchar'
    ];

    private static $has_one = [
        'SocialSharePhoto' => Image::class
    ];

    private static $owns = [
        'SocialSharePhoto'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('Tagline');

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('GTMID', 'Google Tag Manager ID'),
        ]);

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
}
