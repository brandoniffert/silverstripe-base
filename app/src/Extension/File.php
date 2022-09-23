<?php

namespace App\Extension;

use App\Control\FileDownloadController;
use SilverStripe\ORM\DataExtension;

class File extends DataExtension
{
    public function getDownloadLink()
    {
        return FileDownloadController::createLinkFromFile($this->owner);
    }
}
