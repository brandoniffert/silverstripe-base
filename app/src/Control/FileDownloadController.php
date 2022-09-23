<?php

namespace App\Control;

use SilverStripe\Assets\File;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;

class FileDownloadController extends Controller
{
    private static $url_segment = '/_downloadfile';
    protected $template = 'BlankPage';

    private static $allowed_actions = [
        'download'
    ];

    private static $url_handlers = [
        '$FileIDHash!' => 'download'
    ];

    protected function init()
    {
        parent::init();
    }

    public function download($request)
    {
        $fileParts = explode('-', $request->param('FileIDHash'));
        $fileId = $fileParts[0];
        $fileHash = $fileParts[1];

        if ($file = File::get()->filter([
            'ID' => $fileId,
            'FileHash' => $fileHash
        ])->first()) {
            if ($file->canView()) {
                return HTTPRequest::send_file($file->getString(), $file->getFilename(), $file->getMimeType());
            }

            die('Forbidden');
        }

        die('File not found');
    }

    public static function createLinkFromFile($file)
    {
        $url = sprintf('%s/%s-%s', self::$url_segment, $file->ID, $file->FileHash);

        return Director::absoluteURL($url);
    }
}
