<?php

namespace App\View;

use App\Util\AssetUtil;
use SilverStripe\Core\Path;
use SilverStripe\View\Requirements;
use SilverStripe\View\TemplateGlobalProvider;

class AssetTemplateGlobalProvider implements TemplateGlobalProvider
{
    public static function get_template_global_variables()
    {
        return [
            'SiteCSS' => 'getSiteCSS',
            'SiteJS' => 'getSiteJS',
            'Asset' => 'getAsset',
            'AssetInline' => [
                'method' => 'getAssetInline',
                'casting' => 'HTMLFragment',
            ],
            'AssetIcon' => [
                'method' => 'getAssetIcon',
                'casting' => 'HTMLFragment',
            ],
            'AssetIconInline' => [
                'method' => 'getAssetIconInline',
                'casting' => 'HTMLFragment',
            ]
        ];
    }

    public static function getSiteCSS()
    {
        $manifest = AssetUtil::getManifest();
        $manifestAssets = $manifest['entrypoints']['app']['assets'];

        if (array_key_exists('css', $manifestAssets)) {
            foreach ($manifestAssets['css'] as $resource) {
                Requirements::themedCSS(AssetUtil::getResourcePath($resource));
            }
        }
    }

    public static function getSiteJS()
    {
        $manifest = AssetUtil::getManifest();
        $manifestAssets = $manifest['entrypoints']['app']['assets'];

        if (array_key_exists('js', $manifestAssets)) {
            Requirements::set_force_js_to_bottom(true);

            foreach ($manifestAssets['js'] as $resource) {
                if (strpos($resource, 'hot-update') == false) {
                    Requirements::themedJavascript(AssetUtil::getResourcePath($resource));
                }
            }
        }
    }

    /**
     * Returns a path to a resource file.
     *
     * @param mixed $path
     */
    public static function getAsset($path)
    {
        return AssetUtil::getAsset($path);
    }

    /**
     * Returns a file's actual content (only really useful for SVGs).
     *
     * @param mixed $path
     */
    public static function getAssetInline($path)
    {
        return AssetUtil::getAssetInline($path);
    }

    /**
     * Returns an SVG icon based on a spritemap file.
     *
     * Requires this webpack plugin to be used and configured properly
     * https://github.com/cascornelissen/svg-spritemap-webpack-plugin
     *
     * The viewBox attribute is extracted from the spritemap symbol
     * and applied to the parent <svg> so it's easier to work with in CSS
     *
     * @param mixed $name
     */
    public static function getAssetIcon($name, $classes = false)
    {
        return AssetUtil::getAssetIcon($name, $classes);
    }

    /**
     * Returns an SVG icon based on file.
     *
     * @param mixed $name
     */
    public static function getAssetIconInline($name)
    {
        return AssetUtil::getAssetInline($name);
    }
}
