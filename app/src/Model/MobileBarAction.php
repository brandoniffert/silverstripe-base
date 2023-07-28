<?php

namespace App\Model;

use App\Extension\Sortable;
use App\Form\Field\IconPickerField;
use App\Security\CMSPermissionProvider;
use App\Util\Util;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\SiteConfig\SiteConfig;

class MobileBarAction extends DataObject
{
    use CMSPermissionProvider;

    private static $table_name = 'MobileBarAction';
    private static $singular_name = 'Item';
    private static $plural_name = 'Items';

    private static $extensions = [
        Sortable::class
    ];

    private static $db = [
        'Title' => 'Varchar',
        'ActionType' => 'Enum("Internal,External,Phone,Email", "Internal")',
        'ActionExternalLink' => 'Varchar',
        'ActionPhone' => 'Varchar',
        'ActionEmail' => 'Varchar',
        'HideTitle' => 'Boolean'
    ];

    private static $has_one = [
        'Owner' => SiteConfig::class,
        'ActionInternalLink' => SiteTree::class,
        'Icon' => SiteIcon::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'NiceActionType' => 'Link Type'
    ];

    public function getCMSFields()
    {
        $types = [
            SelectionGroup_Item::create(
                'Internal',
                [
                    TreeDropdownField::create('ActionInternalLinkID', '', SiteTree::class)->setHasEmptyDefault(true)
                ],
                'Link to internal page'
            ),
            SelectionGroup_Item::create(
                'External',
                TextField::create('ActionExternalLink', '')->setAttribute('placeholder', 'Enter URL'),
                'Link to external page'
            ),
            SelectionGroup_Item::create(
                'Email',
                EmailField::create('ActionEmail', '')->setAttribute('placeholder', 'Enter an email address'),
                'Email address'
            ),
            SelectionGroup_Item::create(
                'Phone',
                TextField::create('ActionPhone', '')->setAttribute('placeholder', 'Enter a phone number'),
                'Phone Number'
            )
        ];

        $fields = FieldList::create(
            TextField::create('Title'),
            CheckboxField::create('HideTitle', "Don't display title on frontend"),
            IconPickerField::create('Icon'),
            SelectionGroup::create('ActionType', $types)->setTitle('Link Type')
        );

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    public function getCMSValidator()
    {
        return RequiredFields::create([
            'Title',
            'ActionType'
        ]);
    }

    public function validate()
    {
        $result = parent::validate();

        switch ($this->ActionType) {
            case 'Internal':
                if (!$this->ActionInternalLinkID) {
                    $result->addError('Internal Link is required');
                }

                break;
            case 'External':
                if (trim($this->ActionExternalLink) == '') {
                    $result->addError('External Link is required');
                }

                break;
            case 'Email':
                if (trim($this->ActionEmail) == '') {
                    $result->addError('Email is required');
                }

                break;
            case 'Phone':
                if (trim($this->ActionPhone) == '') {
                    $result->addError('Phone Number is required');
                }

                break;
        }

        return $result;
    }

    public function getNiceActionType()
    {
        switch ($this->ActionType) {
            case 'Internal':
                return sprintf('Internal Link (%s)', $this->ActionInternalLink()->Title);
            case 'External':
                return sprintf('External Link (%s)', $this->ActionExternalLink);
            case 'Email':
                return sprintf('Email Address (%s)', $this->ActionEmail);
            case 'Phone':
                return sprintf('Phone Number (%s)', $this->ActionPhone);
        }
    }

    public function getActionLink()
    {
        switch ($this->owner->ActionType) {
            case 'Internal':
                return $this->ActionInternalLink()->Link();
            case 'External':
                return $this->ActionExternalLink;
            case 'Email':
                return "mailto:{$this->ActionEmail}";
            case 'Phone':
                return Util::formatPhone($this->ActionPhone);
        }

        return false;
    }

    public function getIsExternalActionLink()
    {
        $types = [
            'External'
        ];

        return in_array($this->ActionType, $types) != false;
    }
}
