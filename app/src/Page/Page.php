<?php

use SilverStripe\CMS\Model\SiteTree;

class Page extends SiteTree
{
    private static $db = [
        'HeaderTitle' => 'Varchar'
    ];

    private static $has_one = [];
}
