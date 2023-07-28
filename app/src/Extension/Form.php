<?php

namespace App\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;

class Form extends Extension
{
    public function setRequiredFieldPlaceholders(FieldList $fields, $requiredFields)
    {
        foreach ($fields as $field) {
            $name = $field->getName();
            $placeholder = $field->getAttribute('placeholder');

            if (in_array($name, $requiredFields) != false) {
                $type = $field->Type();

                switch ($type) {
                    case 'optionset checkboxset':
                        $field->setTitle("{$field->Title()} *");
                        break;
                    case 'optionset':
                        $field->setTitle("{$field->Title()} *");
                        break;
                    case 'file':
                        $field->setTitle("{$field->Title()} *");
                        break;
                    default:
                        if ($placeholder) {
                            $field->setAttribute('placeholder', "{$placeholder} *");
                        }

                        if ($field->hasMethod('getEmptyString')) {
                            $emptyString = $field->getEmptyString();

                            if ($emptyString) {
                                $field->setEmptyString("{$emptyString} *");
                            }
                        }
                }
            }
        }
    }
}
