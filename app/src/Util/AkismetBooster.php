<?php

namespace App\Util;

use SilverStripe\Control\Director;
use SilverStripe\Core\Environment;

class AkismetBooster
{
    public static function CheckSpam($name, $email, $comments = '')
    {
        $apiKey = Environment::getEnv('SS_AKISMET_API_KEY');
        $Akismet = new CustomAkismet($apiKey);
        $comment_data = [
            'blog' => Director::protocolAndHost(),
            'user_ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'referrer' => '',
            'permalink' => '',
            'comment_type' => '',
            'comment_author' => $name,
            'comment_author_email' => $email,
            'comment_author_url' => '',
            'comment_content' => $comments
        ];

        $response = $Akismet->checkSpam($comment_data);

        if ($response['spam'] == 'true') {
            // let's double check for approved domains and make sure we tell Akismet it's not spam
            $validDomains = [
                'stanidairy.com'
            ];

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailArray = explode('@', $email);
                $domain = array_pop($emailArray);
                $domain = strtolower($domain);

                if (in_array($domain, $validDomains)) {
                    $response = $Akismet->submitHam($comment_data);
                    return false;
                }
            }

            return true;
        } else {
            return false;
        }
    }
}
