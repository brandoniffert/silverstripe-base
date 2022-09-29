<?php

namespace App\Model;

use App\Security\CMSPermissionProvider;
use App\Util\Util;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;

class NavigationMenu extends DataObject
{
    use CMSPermissionProvider;

    private static $default_sort = 'Sort';
    private static $table_name = 'NavigationMenu';

    private static $db = [
        'Title' => 'Varchar',
        'Key' => 'Varchar',
        'Sort' => 'Int'
    ];

    private static $many_many = [
        'Pages' => SiteTree::class
    ];

    private static $many_many_extraFields = [
        'Pages' => [
            'PageSort' => 'Int',
            'ExtraClass' => 'Varchar',
            'MenuTitleOverride' => 'Varchar',
            'AnchorTarget' => 'Varchar'
        ]
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'Pages.Count' => 'Pages'
    ];

    public function getCMSFields()
    {
        $fields = FieldList::create(
            ReadonlyField::create('Title')
        );

        if ($this->exists()) {
            $displayFields = [
                'MenuTitleOverride' => [
                    'title' => 'Menu Title Override',
                    'callback' => function ($record, $column, $grid) {
                        return TextField::create($column);
                    }
                ],
                'AnchorTarget' => [
                    'title' => 'Anchor Target',
                    'callback' => function ($record, $column, $grid) {
                        return TextField::create($column);
                    }
                ]
            ];

            $editableColumns = (new GridFieldEditableColumns)->setDisplayFields($displayFields);

            $fields->push(
                GridField::create(
                    'Pages',
                    'Pages',
                    $this->Pages(),
                    Util::getRelationEditorConfig(true, 'PageSort')
                        ->addComponent($editableColumns, GridFieldEditButton::class)
                )
            );
        }

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    public function getCMSValidator()
    {
        return RequiredFields::create([
            'Title',
            'Key'
        ]);
    }

    public function canCreate($member = null, $context = [])
    {
        return false;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $this->LastEdited = time();
    }
}
