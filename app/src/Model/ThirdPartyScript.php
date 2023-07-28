<?php

namespace App\Model;

use App\Security\CMSPermissionProvider;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\SiteConfig\SiteConfig;

class ThirdPartyScript extends DataObject
{
    use CMSPermissionProvider;

    private static $table_name = 'ThirdPartyScript';
    private static $singular_name = 'Script';
    private static $default_sort = 'Placement DESC';

    private static $db = [
        'Title' => 'Varchar',
        'Code' => 'Text',
        'Enabled' => 'Boolean',
        'Placement' => 'Enum("Head,Body")',
        'OnlyLive' => 'Boolean'
    ];

    private static $has_one = [
        'Owner' => SiteConfig::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'NiceEnabled' => 'Enabled'
    ];

    public function populateDefaults()
    {
        parent::populateDefaults();

        $this->Enabled = true;
        $this->Placement = 'Body';
    }

    public function getCMSFields()
    {
        $fields = FieldList::create(
            FieldGroup::create(
                'Enable',
                CheckboxField::create('Enabled', "Enabled"),
            ),
            FieldGroup::create(
                'Live Only',
                CheckboxField::create('OnlyLive', "Only load when site is in Live mode"),
            ),
            TextField::create('Title'),
            OptionsetField::create(
                'Placement',
                'Placement',
                [
                    'Head' => 'End of <head>',
                    'Body' => 'End of <body>'
                ]
            ),
            TextareaField::create('Code', 'Script Code')->setRows(12)
        );

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    public function getCMSValidator()
    {
        return RequiredFields::create([
            'Title',
            'Code'
        ]);
    }

    public function getNiceEnabled()
    {
        if ($this->Enabled) {
            $label = 'Yes';

            if ($this->OnlyLive) {
                $label .= ' (Live Only)';
            }

            return $label;
        }

        return 'No';
    }
}
