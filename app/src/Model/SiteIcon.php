<?php

namespace App\Model;

use App\Security\CMSPermissionProvider;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Control\Director;
use SilverStripe\Core\Path;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\Queries\SQLUpdate;
use SilverStripe\View\Parsers\URLSegmentFilter;

class SiteIcon extends DataObject
{
    use CMSPermissionProvider;

    private static $default_sort = 'Title';
    private static $table_name = 'SiteIcon';
    private static $singular_name = 'Icon';

    private static $cascade_deletes = [
        'Icon'
    ];

    private static $db = [
        'Title' => 'Varchar',
        'IsCustom' => 'Boolean',
        'PreserveColors' => 'Boolean',
        'Group' => 'Varchar',
        'Raw' => 'HTMLText'
    ];

    private static $has_one = [
        'Icon' => File::class
    ];

    private static $owns = [
        'Icon'
    ];

    private static $summary_fields = [
        'Preview' => 'Icon',
        'Title' => 'Title'
    ];

    private static $searchable_fields = [
        'Title'
    ];

    public function populateDefaults()
    {
        $this->IsCustom = true;
        $this->Group = 'custom';

        parent::populateDefaults();
    }

    public function getCMSFields()
    {
        $fields = FieldList::create(
            TabSet::create('Root')
        );

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Title'),
            UploadField::create('Icon')
                ->setAllowedExtensions(['svg'])
                ->setFolderName('Icons')
                ->setDescription('Must be an svg file'),
            FieldGroup::create(
                'Preserve Colors',
                CheckboxField::create('PreserveColors', 'Preserve any colors in the SVG instead of overwritting')
            )
        ]);

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    public function getCMSValidator()
    {
        return RequiredFields::create([
            'Title',
            'Icon'
        ]);
    }

    public function forTemplate()
    {
        if (!$this->isInDB()) {
            return false;
        }

        return DBField::create_field('HTMLText', $this->Raw);
    }

    public function WithClasses($classes = '')
    {
        $svg = $this->Raw;

        $slug = URLSegmentFilter::create()->filter($this->Title);

        $svg = str_replace('class="site-icon"', "class=\"site-icon {$classes} site-icon-{$slug}\"", $svg);
        $svg = str_replace('class="site-icon site-icon-preserve-colors"', "class=\"site-icon site-icon-preserve-colors {$classes} site-icon-{$slug}\"", $svg);

        return DBField::create_field('HTMLText', $svg);
    }

    public function getPreview()
    {
        if ($this->IsCustom) {
            $svg = $this->Raw;
            $svg = "<div class=\"site-icon-preview\" style=\"color: inherit;\">{$svg}</div>";

            return DBField::create_field('HTMLText', $svg);
        }
    }

    public function validate()
    {
        $result = parent::validate();

        $exists = self::get()->filter([
            'Title' => trim($this->Title)
        ])->exclude('ID', $this->ID);

        if ($exists->count()) {
            $result->addFieldError('Title', 'Icon already exists.');
        }

        return $result;
    }

    public static function prepareThirdpartyFromPath($path)
    {
        $svgHtml = self::optimizeSvg($path, false);

        return $svgHtml;
    }

    public static function optimizeSvg($path, $preserveColors = false)
    {
        $classes = ['site-icon'];

        if ($preserveColors) {
            $classes[] = 'site-icon-preserve-colors';
        }

        $config = [
            'plugins' => [
                'removeXMLProcInst',
                'removeDimensions',
                [
                    'name' => 'addClassesToSVGElement',
                    'params' => [
                        'className' => join(' ', $classes)
                    ]
                ]
            ]
        ];

        if ($preserveColors) {
            $config['plugins'][] = [
                'name' => 'addAttributesToSVGElement',
                'params' => [
                    'attributes' => [
                        'aria-hidden="true"'
                    ]
                ]
            ];
        } else {
            $config['plugins'][] = [
                'name' => 'convertColors',
                'params' => [
                    'currentColor' => true
                ]
            ];

            $config['plugins'][] = [
                'name' => 'addAttributesToSVGElement',
                'params' => [
                    'attributes' => [
                        'aria-hidden="true"',
                        'fill="currentColor"'
                    ]
                ]
            ];
        }

        $jsonConfig = json_encode($config);
        $jsonConfig = "module.exports = {$jsonConfig};";

        $configFilePath = Path::join(
            sys_get_temp_dir(),
            'svgo-config-' . md5($jsonConfig) . '.js'
        );

        if (!file_exists($configFilePath)) {
            file_put_contents($configFilePath, $jsonConfig);
        }

        $svgHtml = shell_exec("$(which svgo) -i {$path} -o - --config {$configFilePath}");

        return $svgHtml;
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if ($this->Icon()->exists() && !$this->Icon()->isPublished()) {
            $this->Icon()->publishSingle();
        }

        if ($this->Icon()->exists() && $this->IsCustom) {
            $path = Director::getAbsFile(
                Path::join(
                    'public/assets',
                    $this->Icon()->FileFilename
                )
            );

            $svgHtml = self::optimizeSvg($path, $this->PreserveColors);

            $update = SQLUpdate::create('"SiteIcon"')->addWhere(['ID' => $this->ID]);
            $update->assign('"Raw"', $svgHtml);
            $update->execute();
        }
    }
}
