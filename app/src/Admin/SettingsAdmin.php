<?php

namespace App\Admin;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\TabSet;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;

abstract class SettingsAdmin extends LeftAndMain
{
    private static $url_rule = '/$Action/$ID/$OtherID';
    protected static $settings = null;

    public function init()
    {
        parent::init();

        if (class_exists(SiteTree::class)) {
            Requirements::javascript('silverstripe/cms: client/dist/js/bundle.js');
        }

        if (is_null(static::$settings)) {
            user_error('Missing property: $settings must be defined on ' . get_called_class(), E_USER_ERROR);
        }
    }

    public function getEditForm($id = null, $fields = null)
    {
        $settings = static::$settings::current_settings();

        $fields = FieldList::create(
            TabSet::create('Root')->setTemplate('SilverStripe\\Forms\\CMSTabSet'),
            HiddenField::create('ID')
        );

        $actions = FieldList::create(
            FormAction::create(
                'save_settings',
                _t(CMSMain::class . '.SAVE', 'Save')
            )->addExtraClass('btn-primary font-icon-save')->setUseButtonTag(true)
        );

        if ($settings->hasMethod('getCMSValidator')) {
            $validator = $settings->getCMSValidator();
        } else {
            $validator = null;
        }

        $negotiator = $this->getResponseNegotiator();

        $form = Form::create(
            $this,
            'EditForm',
            $fields,
            $actions,
            $validator
        )->setHTMLID('Form_EditForm');

        $form->setValidationResponseCallback(function (ValidationResult $errors) use ($negotiator, $form) {
            $request = $this->getRequest();
            if ($request->isAjax() && $negotiator) {
                $result = $form->forTemplate();
                return $negotiator->respond($request, [
                    'CurrentForm' => function () use ($result) {
                        return $result;
                    }
                ]);
            }
        });

        $form->addExtraClass('flexbox-area-grow fill-height cms-content cms-edit-form');
        $form->setAttribute('data-pjax-fragment', 'CurrentForm');
        $form->addExtraClass('ss-tabset cms-tabset ' . $this->BaseCSSClasses());
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

        // Add custom tabs/fields before the default 'Settings' tab
        if (method_exists($this, 'updateEditFormBefore')) {
            $this->updateEditFormBefore($form);
        }

        $fields->addFieldsToTab('Root.Settings', $settings->getCMSFields());

        if ($tab = $fields->findTab('Root.Settings')) {
            if ($tabName = $settings->config()->get('tab_name')) {
                $tab->setTitle($tabName);
            }
        }

        // Add custom tabs/fields after the default 'Settings' tab
        if (method_exists($this, 'updateEditFormAfter')) {
            $this->updateEditFormAfter($form);
        }

        $form->loadDataFrom($settings);
        $fields->setForm($form);

        return $form;
    }

    public function save_settings($data, $form)
    {
        $data = $form->getData();
        $settings = static::$settings::current_settings();
        $form->saveInto($settings);
        $settings->write();
        $this->response->addHeader('X-Status', rawurlencode(_t(LeftAndMain::class . '.SAVEDUP', 'Saved.')));
        return $form->forTemplate();
    }

    public function Breadcrumbs($unlinked = false)
    {
        return new ArrayList([
            new ArrayData([
                'Title' => static::menu_title(),
                'Link' => $this->Link()
            ])
        ]);
    }
}
