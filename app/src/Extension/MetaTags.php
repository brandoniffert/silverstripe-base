<?php

namespace App\Extension;

use App\Util\TextUtil;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\ArrayData;

class MetaTags extends DataExtension
{
    private static $db = [
        'MetaTitle' => 'Varchar',
        'MetaDescription' => 'Text'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.Main', [
            ToggleCompositeField::create(
                'Metadata',
                'Metadata',
                [
                    TextField::create('MetaTitle', 'Meta Title'),
                    TextareaField::create('MetaDescription', 'Meta Description')
                ]
            )->setHeadingLevel(4)
        ]);
    }

    public function getMetaTags($defaultTitle = 'Title', $defaultDescription = ['Content'], $image = false)
    {
        $title = false;
        $description = false;

        if ($this->owner->MetaTitle) {
            $title = $this->owner->MetaTitle;
        } elseif ($this->owner->hasMethod('getCustomMetaTitle')) {
            $title = $this->owner->getCustomMetaTitle();
        } elseif ($this->owner->hasField($defaultTitle)) {
            $title = $this->owner->getField($defaultTitle);
        }

        if ($this->owner->MetaDescription) {
            $description = $this->owner->MetaDescription;
        } elseif ($this->owner->hasMethod('getCustomMetaDescription')) {
            $description = $this->owner->getCustomMetaDescription();
        } else {
            foreach ($defaultDescription as $field) {
                if ($this->owner->hasField($field)) {
                    if ($value = $this->owner->getField($field)) {
                        $description = $value;
                        break;
                    }
                }
            }
        }

        if ($this->owner->hasMethod('getCustomMetaImage')) {
            $image = $this->owner->getCustomMetaImage();
        }

        if ($description) {
            if (gettype($description) == 'string') {
                $description = DBField::create_field('HTMLText', $description);
            }

            $description = $description->LimitCharactersToClosestWord(250, '');
        }

        $data = [
            'MetaTitle' => TextUtil::deemphasize(trim($title)),
            'MetaDescription' => trim($description)
        ];

        if ($image && $image->isInDB()) {
            $data['MetaImage'] = $image;
        }

        return ArrayData::create($data);
    }
}
