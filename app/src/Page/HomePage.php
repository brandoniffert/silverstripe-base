<?php

namespace App\Page;

use App\Extension\SinglePageInstance;
use Page;

class HomePage extends Page
{
    private static $table_name = 'HomePage';
    private static $singular_name = 'Home';
    private static $plural_name = 'Home';
    private static $icon_class = 'font-icon-home';
    private static $description = 'The main Home Page for the site';

    private static $extensions = [
        SinglePageInstance::class
    ];

    private static $db = [];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        return $fields;
    }
}
