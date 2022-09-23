<?php

namespace App\Admin;

use SilverStripe\Admin\ModelAdmin as SS_ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Helper class for ModelAdmins
 *
 * Modifies the EditForm's GridField to fit most common cases and adds
 * sorting if it detects a `Sort` column on the modelClass
 */
abstract class ModelAdmin extends SS_ModelAdmin
{
    public $showImportForm = false;

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        if ($grid = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass))) {
            $grid->getConfig()->removeComponentsByType(GridFieldExportButton::class);
            $grid->getConfig()->removeComponentsByType(GridFieldPrintButton::class);
            $grid->getConfig()->getComponentByType(GridFieldPaginator::class)->setItemsPerPage(100);

            if (array_key_exists('Sort', singleton($this->modelClass)->config()->db)) {
                $grid->getConfig()->addComponent(GridFieldOrderableRows::create());
            }
        }

        return $form;
    }
}
