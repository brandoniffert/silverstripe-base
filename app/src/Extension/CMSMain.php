<?php

namespace App\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\ErrorPage\ErrorPage;
use SilverStripe\ORM\DB;

class CMSMain extends Extension
{
    /**
     * Always keep the ErrorPages at the bottom of the CMS SiteTree list when adding a new top-level page
     */
    public function augmentNewSiteTreeItem($item)
    {
        if ($item->ParentID == 0) {
            $maxSort = DB::prepared_query(
                'SELECT MAX("Sort") FROM "SiteTree" WHERE "ParentID" = ?',
                [$item->ParentID]
            )->value();

            $errorPageOffset = 2;

            foreach (ErrorPage::get() as $errorPage) {
                $errorPage->Sort = $maxSort + $errorPageOffset;
                $errorPage->write();
                $errorPage->publishRecursive();

                $errorPageOffset++;
            }
        }
    }
}
