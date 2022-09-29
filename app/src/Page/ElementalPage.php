<?php

namespace App\Page;

use DNADesign\Elemental\Extensions\ElementalPageExtension;
use DNADesign\Elemental\Models\ElementContent;
use Page;

class ElementalPage extends Page
{
    private static $table_name = 'ElementalPage';
    private static $description = 'Generic page to build out with element blocks';
    private static $singular_name = 'Element Page';

    private static $extensions = [
        ElementalPageExtension::class
    ];

    private static $disallowed_elements = [
        ElementContent::class
    ];
}
