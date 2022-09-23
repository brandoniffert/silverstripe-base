<?php

namespace App\Util;

use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordViewer;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Forms\GridField\GridFieldViewButton;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\SS_List;
use SilverStripe\Versioned\GridFieldArchiveAction;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class Util
{
    /**
     * Returns a LiteralField styled as a message.
     */
    public static function cmsInfoMessage(string $message, bool $noMargin = false)
    {
        return LiteralField::create(
            'CMSInfoMessage',
            sprintf(
                '<div class="alert alert-info" role="alert" style="%s">%s</div>',
                $noMargin ? '' : 'margin-bottom: 30px',
                $message
            )
        );
    }

    /**
     * Returns a LiteralField styled as a warning.
     */
    public static function cmsWarningMessage(string $message, bool $noMargin = false)
    {
        return LiteralField::create(
            'CMSWarningMessage',
            sprintf(
                '<div class="alert alert-warning" role="alert" style="%s">%s</div>',
                $noMargin ? '' : 'margin-bottom: 30px',
                $message
            )
        );
    }

    /**
     * Helper for building up a GridFieldConfig_RecordEditor config with optional sorting.
     */
    public static function getRecordEditorConfig(bool $sortable = true)
    {
        $config = GridFieldConfig_RecordEditor::create(100);

        if ($sortable) {
            $config->addComponent(GridFieldOrderableRows::create());
        }

        return $config;
    }

    /**
     * Helper for building up a GridFieldConfig_RecordViewer config.
     */
    public static function getRecordViewerConfig(bool $noLink = false)
    {
        $config = GridFieldConfig_RecordViewer::create(100);

        if ($noLink) {
            $config->removeComponentsByType(GridFieldViewButton::class);
            $config->removeComponentsByType(GridFieldSortableHeader::class);
        }

        return $config;
    }

    /**
     * Helper for building up a GridFieldConfig_RelationEditor config with optional sorting.
     */
    public static function getRelationEditorConfig(bool $sortable = true, string $sortColumn = 'Sort')
    {
        $config = GridFieldConfig_RelationEditor::create(100);
        $config->removeComponentsByType(GridFieldAddNewButton::class);
        $config->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        $config->removeComponentsByType(GridFieldArchiveAction::class);
        $config->addComponent(new GridFieldAddExistingSearchButton());

        if ($sortable) {
            $config->addComponent(GridFieldOrderableRows::create($sortColumn));
        }

        return $config;
    }

    /**
     * Parses a YouTube ID from a given $url.
     */
    public static function getYouTubeID(string $url)
    {
        $pattern = '%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';

        if (preg_match($pattern, $url, $match)) {
            return $match[1];
        }

        return false;
    }

    /**
     * Parses a Vimeo ID from a given $url.
     */
    public static function getVimeoID(string $url)
    {
        $pattern = '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/';

        if (preg_match($pattern, $url, $match)) {
            return $match[5];
        }

        return false;
    }

    /**
     * Useful for taking a bare array of string values and allowing them
     * to be iterated over and rendered in a template (via ArrayList).
     */
    public static function array2ArrayList(array $arr)
    {
        $list = ArrayList::create();

        foreach ($arr as $item) {
            $text = DBField::create_field('Varchar', $item);
            $list->push($text);
        }

        return $list;
    }

    /**
     * Removes any visual separators/spaces in a phone number, generally used
     * to get a formatted number for a tel: anchor tag.
     */
    public static function cleanPhoneNumber(string $number)
    {
        $clean = strtolower(trim(preg_replace('/(\s|\.|\-|\(|\))/', '', $number)));

        return preg_replace('/ext\.?/', 'x', $clean);
    }

    /**
     * Generates a breadcrumbs element with a given array of parent pages
     * and a title for the current page.
     */
    public static function getFakeBreadcrumbs(array $parents, ?string $currentTitle)
    {
        if (is_null($currentTitle)) {
            return false;
        }

        $pages = ArrayList::create(array_reverse($parents));

        $truncatedTitle = TextUtil::truncate($currentTitle);

        $pages->push(ArrayData::create([
            'MenuTitle' => $truncatedTitle,
        ]));

        $template = SSViewer::create('Includes/Breadcrumbs');

        return $template->process(ArrayData::create([
            'Pages' => $pages,
            'Delimiter' => '/',
        ]));
    }

    /**
     * Given a list and an item in that list, return the previous and next items.
     */
    public static function getItemNeighbors(SS_List $list, DataObject $item)
    {
        $prev = null;
        $next = null;

        $ids = array_keys($list->map('ID', 'ClassName')->toArray());
        $index = array_search($item->ID, $ids);

        if (array_key_exists($index - 1, $ids)) {
            $prev = $list->byID($ids[$index - 1]);
        }

        if (array_key_exists($index + 1, $ids)) {
            $next = $list->byID($ids[$index + 1]);
        }

        return ArrayData::create([
            'Prev' => $prev,
            'Next' => $next,
        ]);
    }

    /**
     * Chunk a list into $chunks lists, favoring more items towards the left
     * Ex: Start list => [1, 2, 3, 4, 5, 6, 7] ($chunks = 3)
     *     End list => [[1, 2, 3], [4, 5, 6], [7]].
     */
    public static function chunkList(SS_List $list, int $chunks = 2, int $perChunk = null)
    {
        if (!$list) {
            return false;
        }

        if ($list->count() < $chunks) {
            $lists = ArrayList::create();
            $lists->push($list);

            return $lists;
        }

        $lists = ArrayList::create();
        $itemsPerList = ceil($list->count() / $chunks);

        if (!is_null($perChunk)) {
            $itemsPerList = $perChunk;
        }

        $chunked = array_chunk($list->toArray(), $itemsPerList);

        foreach ($chunked as $items) {
            $list = ArrayList::create();

            foreach ($items as $item) {
                $list->push($item);
            }

            $lists->push($list);
        }

        return $lists;
    }

    public static function unflattenArray($data)
    {
        $output = [];

        foreach ($data as $key => $value) {
            $parts = explode('-', $key);
            $nested = &$output;
            while (count($parts) > 1) {
                $nested = &$nested[array_shift($parts)];
                if (!is_array($nested)) {
                    $nested = [];
                }
            }
            $nested[array_shift($parts)] = $value;
        }

        return $output;
    }
}
