<?php

namespace App\Model;

use App\Extension\CallToActionable;
use App\Extension\Sortable;
use App\Security\CMSPermissionProvider;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use UncleCheese\DisplayLogic\Forms\Wrapper;

class CallToAction extends DataObject
{
    use CMSPermissionProvider;

    private static $table_name = 'CallToAction';

    private static $extensions = [
        CallToActionable::class,
        Sortable::class
    ];

    private static $db = [
        'ActionTitle' => 'Varchar',
        'Style' => 'Varchar'
    ];

    private static $has_one = [
        'Owner' => DataObject::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'NiceActionType' => 'Link Type'
    ];

    public function populateDefaults()
    {
        parent::populateDefaults();

        $this->Style = 'btn-fill';
    }

    public function getCMSFields()
    {
        $fields = FieldList::create();

        $this->extend('updateCMSFields', $fields);

        $fields->push(
            Wrapper::create(
                TextField::create('ActionTitle', 'Title'),
                DropdownField::create(
                    'Style',
                    'Style',
                    [
                        'btn-fill' => 'Solid',
                        'btn-hollow' => 'Hollow',
                    ],
                )
            )
        );

        return $fields;
    }

    public function validate()
    {
        $result = parent::validate();

        switch ($this->ActionType) {
            case 'Internal':
                if (trim($this->ActionTitle) == '') {
                    $result->addError('Title is required');
                }

                break;
            case 'Anchor':
                if (trim($this->ActionTitle) == '') {
                    $result->addError('Title is required');
                }

                break;
            case 'External':
                if (trim($this->ActionTitle) == '') {
                    $result->addError('Title is required');
                }

                break;
        }

        $this->extend('updateValidate', $result);

        return $result;
    }

    public function getTitle()
    {
        if ($this->ActionTitle) {
            return $this->ActionTitle;
        }

        switch ($this->ActionType) {
            case 'Phone':
                return $this->ActionPhone;
                break;
        }
    }

    public function getClasses()
    {
        $classes = [];

        if ($this->Style == 'btn-fill') {
            $classes = array_merge($classes, ['btn', 'btn-fill']);
        }

        if ($this->Style == 'btn-hollow') {
            $classes = array_merge($classes, ['btn', 'btn-hollow']);
        }

        return join(' ', $classes);
    }
}
