<?php

namespace App\Extension;

use App\Util\AssetUtil;
use SilverStripe\Core\Extension;

class Controller extends Extension
{
    public function requireManifestAssets($name)
    {
        AssetUtil::requireManifestAssets($name);
    }
}
