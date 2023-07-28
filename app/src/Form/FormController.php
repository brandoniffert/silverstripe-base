<?php

namespace App\Form;

use App\Email\Mailer;
use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use SilverStripe\Security\SecurityToken;

class FormController extends Controller
{
    private static $allowed_actions = [
        'handleemail',
        'handleresponse',
        'handleerrorresponse'
    ];

    private static $url_segment = '/_formsubmit';

    public function init()
    {
        parent::init();
    }

    public function handleemail($success, $submission, $subject, $formSettings, $attachments = [])
    {
        if (!$success) {
            return false;
        }

        // Send admin email
        $adminRecipients = $formSettings->EmailRecipients()->column('Email');

        // Allow additional recipients
        if ($formSettings->AdditionalRecipients && $formSettings->AdditionalRecipients->count()) {
            $adminRecipients = array_merge($adminRecipients, $formSettings->AdditionalRecipients->column('Email'));
            $adminRecipients = array_unique($adminRecipients);
        }

        if (count($adminRecipients)) {
            $to = $adminRecipients;

            Mailer::sendNewSubmissionToAdmin($submission, $to, $subject, null, $attachments);
        }

        // Send autoresponder to user
        if ($formSettings->AutoresponderEnabled) {
            $subject = $formSettings->AutoresponderSubject;
            $body = $formSettings->AutoresponderContent;

            Mailer::sendAutoresponderToUser($submission->Email, $subject, $body);
        }
    }

    public function handleresponse($success, $formSettings, $extraResponse = [])
    {
        if (Director::is_ajax($this->getRequest())) {
            if (!SecurityToken::inst()->checkRequest($this->getRequest())) {
                return $this->httpError(400);
            }

            if ($success) {
                $message = $formSettings->SubmitSuccessMessage;
            } else {
                $message = 'Sorry, there was a problem with your submission';
            }

            $this->getResponse()->addHeader('Content-Type', 'application/json');

            $response = [
                'success' => $success,
                'message' => $message
            ];

            if (!empty($extraResponse)) {
                $response = array_merge($response, $extraResponse);
            }

            return json_encode($response);
        }

        return $this->redirectBack();
    }

    public function handlesubscriberesponse($success, $message, $extraResponse = [])
    {
        if (Director::is_ajax($this->getRequest())) {
            if (!SecurityToken::inst()->checkRequest($this->getRequest())) {
                return $this->httpError(400);
            }

            $this->getResponse()->addHeader('Content-Type', 'application/json');

            $response = [
                'success' => $success,
                'message' => $message,
            ];

            if (!empty($extraResponse)) {
                $response = array_merge($response, $extraResponse);
            }

            return json_encode($response);
        }

        return $this->redirectBack();
    }

    public function handleerrorresponse($message, $fieldErrors = [])
    {
        if (Director::is_ajax($this->getRequest())) {
            if (!SecurityToken::inst()->checkRequest($this->getRequest())) {
                return $this->httpError(400);
            }

            $this->getResponse()->addHeader('Content-Type', 'application/json');

            $response = [
                'success' => false,
                'message' => $message,
                'field_errors' => $fieldErrors
            ];

            return json_encode($response);
        }

        return $this->redirectBack();
    }
}
