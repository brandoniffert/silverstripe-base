<?php

namespace App\Util;

use SilverStripe\Control\Director;
use SilverStripe\Core\Path;
use SilverStripe\View\Requirements;

class AssetUtil
{
    private static $manifestCache = null;
    private static $assetIconSymbolsCache = null;
    private static $assetIconInlineCache = [];
    private static $spritemapFile = 'spritemap.svg';
    private static $themeDir = 'themes/app';

    /**
     * Returns a path to a resource file.
     *
     * @param mixed $path
     */
    public static function getAsset($path)
    {
        return self::getResourcePath($path);
    }

    /**
     * Returns a file's actual content (only really useful for SVGs).
     *
     * @param mixed $path
     */
    public static function getAssetInline($path)
    {
        return file_get_contents(self::getAbsResourcePath($path));
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
        $spritemapPath = self::getAsset(self::$spritemapFile);
        $symbols = self::getAssetIconSymbols();

        // Special cases where we want to preserve the colors of the original SVG
        $useImage = [];

        if (in_array($name, $useImage)) {
            return self::getAssetIconInline($name);
        }

        if (array_key_exists("sprite-{$name}", $symbols)) {
            $symbol = $symbols["sprite-{$name}"];

            return sprintf('
                <svg data-icon="%s" aria-hidden="true" fill="currentColor" viewBox="%s" %s>
                    <use xlink:href="%s#sprite-%s"></use>
                </svg>
            ', $name, $symbol->getAttribute('viewBox'), $classes ? "class=\"{$classes}\"" : '', $spritemapPath, $name);
        }
    }

    /**
     * Returns an SVG icon based on file.
     *
     * @param mixed $name
     */
    public static function getAssetIconInline($name)
    {
        if (array_key_exists($name, self::$assetIconInlineCache)) {
            return self::$assetIconInlineCache[$name];
        }

        $svg = new \DomDocument();
        $svg->validateOnParse = true;
        $svg->load(self::getAbsResourcePath("images/{$name}.svg"));
        $svg->getElementsByTagName('svg')[0]->setAttribute('data-icon', $name);

        $svgHtml = $svg->saveHTML();

        self::$assetIconInlineCache[$name] = $svgHtml;

        return $svgHtml;
    }

    /**
     * Requires a js and css file from the webpack assests-manifest.json file
     *
     * @param mixed $name
     */
    public static function requireManifestAssets($name)
    {
        Requirements::set_force_js_to_bottom(true);

        $manifest = self::getManifest();

        if (array_key_exists($name, $manifest['entrypoints'])) {
            $assets = $manifest['entrypoints'][$name]['assets'];

            if (array_key_exists('css', $assets)) {
                foreach ($assets['css'] as $resource) {
                    Requirements::themedCSS(self::getResourcePath($resource));
                }
            }

            if (array_key_exists('js', $assets)) {
                foreach ($assets['js'] as $resource) {
                    Requirements::themedJavascript(self::getResourcePath($resource));
                }
            }
        }
    }

    /**
     * Requires a css file from the webpack assests-manifest.json file
     *
     * @param mixed $name
     */
    public static function requireManifestCSS($name)
    {
        $manifest = self::getManifest();

        if (array_key_exists($name, $manifest['entrypoints'])) {
            $assets = $manifest['entrypoints'][$name]['assets'];

            if (array_key_exists('css', $assets)) {
                foreach ($assets['css'] as $resource) {
                    Requirements::themedCSS(self::getResourcePath($resource));
                }
            }
        }
    }

    /**
     * Requires a js file from the webpack assests-manifest.json file
     *
     * @param mixed $name
     */
    public static function requireManifestJS($name)
    {
        Requirements::set_force_js_to_bottom(true);

        $manifest = self::getManifest();

        if (array_key_exists($name, $manifest['entrypoints'])) {
            $assets = $manifest['entrypoints'][$name]['assets'];

            if (array_key_exists('js', $assets)) {
                foreach ($assets['js'] as $resource) {
                    Requirements::themedJavascript(self::getResourcePath($resource));
                }
            }
        }
    }

    /**
     * Returns a path to a file relative to the theme's resources folder (usually the dist or output directory).
     *
     * @param mixed $path
     */
    public static function getResourcePath($path)
    {
        return Path::join(
            '/',
            RESOURCES_DIR,
            self::$themeDir,
            'dist',
            $path
        );
    }

    /**
     * Returns an absolute path to a file on disk.
     *
     * @param mixed $path
     */
    public static function getAbsResourcePath($path)
    {
        $resourcePath = Director::makeRelative(self::getResourcePath($path));

        return Director::getAbsFile($resourcePath);
    }

    /**
     * Loads all the symbols (icons) in the SVG spritemap file.
     */
    private static function getAssetIconSymbols()
    {
        if (!is_null(self::$assetIconSymbolsCache)) {
            return self::$assetIconSymbolsCache;
        }

        $spritemapAbsPath = self::getAbsResourcePath(self::$spritemapFile);

        if (!file_exists($spritemapAbsPath)) {
            return [];
        }

        $spritemap = new \DomDocument();
        $spritemap->validateOnParse = true;
        $spritemap->load($spritemapAbsPath);
        $symbolNodes = $spritemap->getElementsByTagName('symbol');
        $symbols = [];

        foreach ($symbolNodes as $node) {
            $symbols[$node->getAttribute('id')] = $node;
        }

        self::$assetIconSymbolsCache = $symbols;

        return self::$assetIconSymbolsCache;
    }

    /**
     * Loads the manifest.json file so the paths are available for Requirements.
     *
     * Requires this webpack plugin to be used and configured properly
     * https://github.com/webdeveric/webpack-assets-manifest
     */
    public static function getManifest()
    {
        if (!is_null(self::$manifestCache)) {
            return self::$manifestCache;
        }

        $manifestFile = self::getAbsResourcePath('assets-manifest.json');

        if (file_exists($manifestFile)) {
            $contents = json_decode(file_get_contents($manifestFile), true);
            self::$manifestCache = $contents;
        } else {
            self::$manifestCache = false;
        }

        return self::$manifestCache;
    }
}
