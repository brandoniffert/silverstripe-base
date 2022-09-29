<?php

namespace App\Extension;

use App\Util\Util;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

class BaseElement extends DataExtension
{
    private static $db = [
        'AnchorOverride' => 'Varchar',
        'IsHidden' => 'Boolean'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('IsHidden');

        $fields->addFieldsToTab('Root.Settings', [
            FieldGroup::create(
                'Visibility',
                CheckboxField::create('IsHidden', 'Hide this element on the page')
            ),
            TextField::create('AnchorOverride')
        ]);
    }

    public function getHolderClasses()
    {
        $targetElement = $this->owner;

        $classParts = explode('\\', $targetElement->ClassName);
        $className = array_pop($classParts);
        $elementClass = sprintf('element-%s', strtolower($className));

        $isFirst = $this->owner->First();
        $isLast = $this->owner->Last();

        $classes = [$elementClass, 'element-section'];

        if ($isFirst) {
            $classes[] = 'element-section--first';
        }

        if ($isLast && !$isFirst) {
            $classes[] = 'element-section--last';
        }

        if ($isFirst && $isLast) {
            $classes[] = 'element-section--only';
        }

        if ($this->owner->StyleVariant) {
            $classes[] = $this->owner->StyleVariant;
        }

        $customPanelClass = $this->owner->getCustomPanelClass();

        if (is_array($customPanelClass)) {
            $classes = array_merge($classes, $customPanelClass);
        }

        return join(' ', $classes);
    }

    public function getCustomPanelClass()
    {
        return [];
    }

    public function getHideIf()
    {
        return $this->owner->IsHidden;
    }

    public function getRecordEditorConfig($sortable = true)
    {
        return Util::getRecordEditorConfig($sortable);
    }

    public function getRelationEditorConfig($sortable = true, $sortColumn = 'Sort')
    {
        return Util::getRelationEditorConfig($sortable, $sortColumn);
    }

    public function getScrollOffset()
    {
        return 24;
    }
}
