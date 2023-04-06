<?php

namespace App\Form\Field;

use App\Model\SiteIcon;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;

class IconPickerField extends DropdownField
{
    private static $casting = [
        'getIconList' => 'HTMLFragment'
    ];

    protected $list = [];

    public function __construct($name, $title = null, $source = [], $value = null)
    {
        if (strrpos($name, 'ID') === false) {
            if (is_null($title)) {
                $title = $name;
            }

            $name = "{$name}ID";
        }

        if (empty($source)) {
            $this->setList(SiteIcon::get()->map('ID', 'Title')->toArray());
        }

        $this->setEmptyString('Select an Icon');

        parent::__construct($name, $title, $source, $value);
    }

    public function setList($list)
    {
        $this->list = $list;

        return $this;
    }

    public function getSource()
    {
        return $this->list;
    }

    public function Field($properties = [])
    {
        Requirements::css('app/cms/iconpickerfield/iconpickerfield.css');
        Requirements::javascript('app/cms/iconpickerfield/iconpickerfield.js');

        return parent::Field($properties);
    }

    public function extraClass()
    {
        $classes[] = parent::extraClass();

        $classes[] .= 'dropdown';

        return implode(' ', $classes);
    }

    public function getIconList()
    {
        $template = '';

        $icons = SiteIcon::get()->sort('IsCustom', 'DESC')->sort('Title', 'ASC');

        foreach ($icons as $icon) {
            $title = str_replace('fa-light-', '', $icon->Title);
            $template .= sprintf('<li data-title="%s"><button type="button" data-id="%s" title="%s" class="iconpickerfield-option">%s</button></li>', $title, $icon->ID, $title, $icon->Raw);
        }

        $html = DBHTMLText::create();
        $html->setValue($template);

        return $html;
    }

    public function getSelectedItem()
    {
        if ($this->Value()) {
            return DBField::create_field('HTMLFragment', SiteIcon::get()->byID($this->Value())->Raw);
        }

        return false;
    }
}
