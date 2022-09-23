<?php

namespace App\Security;

use SilverStripe\Security\Permission;

trait CMSPermissionProvider
{
    public function canCreate($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_CMSMain');
    }

    public function canEdit($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_CMSMain');
    }

    public function canDelete($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_CMSMain');
    }

    public function canView($member = null, $context = [])
    {
        return true;
    }

    public function canPublish($member = null, $context = [])
    {
        return true;
    }
}
