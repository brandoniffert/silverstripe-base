<?php

namespace App\Email;

use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;

class EmailRecipient extends DataObject
{
    private static $table_name = 'EmailRecipient';

    private static $db = [
        'Email' => 'Varchar(100)'
    ];

    private static $has_one = [
        'Owner' => DataObject::class
    ];

    private static $summary_fields = [
        'Email'
    ];

    public function getTitle()
    {
        return $this->Email;
    }

    public function getCMSFields()
    {
        return FieldList::create(
            EmailField::create('Email')
        );
    }

    public function getCMSValidator()
    {
        return RequiredFields::create([
            'Email',
        ]);
    }

    public function validate()
    {
        $result = parent::validate();

        if (!filter_var($this->Email, FILTER_VALIDATE_EMAIL)) {
            $result->addFieldError('Email', 'Please enter a valid email address');
        }

        return $result;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $this->Email = trim($this->Email);
    }
}
