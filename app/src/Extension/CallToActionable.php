<?php

namespace App\Extension;

use App\Form\Field\DBVideoURL;
use App\Util\Util;
use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ValidationResult;

class CallToActionable extends DataExtension
{
    private static $db = [
        'ActionType' => 'Enum("Internal,Anchor,File,Video,External,Element,Phone,Email", "Internal")',
        'ActionAnchorTarget' => 'Varchar',
        'ActionExternalLink' => 'Varchar',
        'ActionSectionIdentifier' => 'Varchar',
        'ActionInternalLinkAnchorTarget' => 'Varchar',
        'ActionVideoURL' => DBVideoURL::class,
        'ActionEmail' => 'Varchar',
        'ActionPhone' => 'Varchar'
    ];

    private static $has_one = [
        'ActionInternalLink' => SiteTree::class,
        'ActionFile' => File::class,
        'ActionElement' => BaseElement::class
    ];

    private static $owns = [
        'ActionFile'
    ];

    public function populateDefaults()
    {
        parent::populateDefaults();

        $this->owner->ActionType = 'Internal';
    }

    public function updateCMSFields(FieldList $fields)
    {
        $controller = Controller::curr();
        $currentPage = $controller->getRecord($controller->currentPageID());

        $types = [
            SelectionGroup_Item::create(
                'Internal',
                [
                    TreeDropdownField::create('ActionInternalLinkID', '', SiteTree::class)->setHasEmptyDefault(true),
                    TextField::create('ActionInternalLinkAnchorTarget', '')
                        ->setDescription('Optionally include a query param for the linked page')
                        ->setAttribute('placeholder', 'Enter additional query param'),
                ],
                'Link to internal page'
            ),
            SelectionGroup_Item::create(
                'Anchor',
                TextField::create('ActionAnchorTarget', '')->setAttribute('placeholder', 'Enter anchor element (with #)'),
                'Link to page anchor'
            ),
            SelectionGroup_Item::create(
                'External',
                TextField::create('ActionExternalLink', '')->setAttribute('placeholder', 'Enter URL'),
                'Link to external page'
            ),
            SelectionGroup_Item::create(
                'Email',
                EmailField::create('ActionEmail', '')->setAttribute('placeholder', 'Enter an email address'),
                'Email address'
            ),
            SelectionGroup_Item::create(
                'Video',
                [
                    TextField::create('ActionVideoURL', '')->showYouTubeHelper()
                ],
                'YouTube Video'
            ),
            SelectionGroup_Item::create(
                'File',
                UploadField::create('ActionFile', '')
                    ->setAllowedFileCategories('document')
                    ->setFolderName('Documents'),
                'File Download Link'
            ),
            SelectionGroup_Item::create(
                'Phone',
                TextField::create('ActionPhone', '')->setAttribute('placeholder', 'Enter a phone number'),
                'Phone Number'
            )
        ];

        if ($currentPage && $currentPage->hasMethod('ElementalArea')) {
            $pageElements = $currentPage->ElementalArea()->Elements();

            if ($pageElements->count()) {
                $types[] = SelectionGroup_Item::create(
                    'Element',
                    DropdownField::create(
                        'ActionElementID',
                        '',
                        $pageElements->map('ID', 'SearchDisplayName')
                    )->setEmptyString('Select One'),
                    'Element on this page'
                );
            }
        }

        $fields->push(SelectionGroup::create('ActionType', $types)->setTitle('Link Type'));

        $fields->push(HiddenField::create(
            'ActionSectionIdentifier',
            'ActionSectionIdentifier',
            Controller::curr()->getRequest()->param('FieldName')
        ));
    }

    public function getNiceActionType()
    {
        switch ($this->owner->ActionType) {
            case 'Internal':
                return sprintf('Internal Link (%s)', $this->owner->ActionInternalLink()->Title);
            case 'Anchor':
                return sprintf('Page Anchor (%s)', $this->owner->ActionAnchorTarget);
            case 'External':
                return sprintf('External Link (%s)', $this->owner->ActionExternalLink);
            case 'File':
                return 'File Download';
            case 'Email':
                return sprintf('Email Address (%s)', $this->owner->ActionEmail);
            case 'Video':
                return 'Video Link';
            case 'Phone':
                return sprintf('Phone Number (%s)', $this->owner->ActionPhone);
            case 'Element':
                return sprintf('Page Element (%s)', $this->owner->ActionElement()->Title);
        }
    }

    public function getActionLink()
    {
        switch ($this->owner->ActionType) {
            case 'Internal':
                $link = $this->owner->ActionInternalLink()->Link();

                if ($this->owner->ActionInternalLinkAnchorTarget) {
                    $link .= $this->owner->ActionInternalLinkAnchorTarget;
                }
                return $link;
            case 'Anchor':
                return $this->owner->ActionAnchorTarget;
            case 'External':
                return $this->owner->ActionExternalLink;
            case 'Email':
                return "mailto:{$this->owner->ActionEmail}";
            case 'File':
                return $this->owner->ActionFile()->getDownloadLink();
            case 'Video':
                return $this->owner->ActionVideoURL;
            case 'Phone':
                return Util::formatPhone($this->owner->ActionPhone);
            case 'Element':
                if ($this->owner->ActionElement()->AnchorOverride) {
                    return $this->owner->ActionElement()->AnchorOverride;
                }

                return '#' . $this->owner->ActionElement()->getAnchor();
        }

        return false;
    }

    public function getActionAttrs()
    {
        $attrs = [];

        switch ($this->owner->ActionType) {
            case 'Internal':
                if ($this->owner->ActionInternalLink()->exists() && $this->owner->ActionInternalLink()->IsExternalRedirector()) {
                    $attrs = ['target="_blank"', 'rel="noopener noreferrer"'];
                }

                return join(' ', $attrs);
            case 'Anchor':
                $attrs = [];
                return join(' ', $attrs);
            case 'External':
                $attrs = ['target="_blank"', 'rel="noopener noreferrer"'];
                return join(' ', $attrs);
            case 'Email':
                $attrs = [];
                return join(' ', $attrs);
            case 'File':
                $attrs = ['target="_blank"', 'rel="noopener noreferrer"'];
                return join(' ', $attrs);
            case 'Video':
                $attrs = ['data-modal="video"', 'target="_blank"', 'rel="noopener noreferrer"'];
                return join(' ', $attrs);
            case 'Phone':
                $attrs = [];
                return join(' ', $attrs);
            case 'Element':
                $attrs = ['data-scroll'];
                return join(' ', $attrs);
        }

        return join(' ', $attrs);
    }

    public function getIsVideo()
    {
        return $this->owner->ActionType == 'Video';
    }

    public function getIsExternalActionLink()
    {
        if ($this->owner->ActionInternalLink()->exists() && $this->owner->ActionInternalLink()->IsExternalRedirector()) {
            return true;
        }

        $types = [
            'External',
            'Video',
            'Email'
        ];

        return in_array($this->owner->ActionType, $types) != false;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if ($this->owner->ActionType != 'Internal') {
            $this->owner->ActionInternalLinkID = null;
            $this->owner->ActionInternalLinkAnchorTarget = null;
        }

        if ($this->owner->ActionType != 'Anchor') {
            $this->owner->ActionAnchorTarget = null;
        }

        if ($this->owner->ActionType != 'External') {
            $this->owner->ActionExternalLink = null;
        }

        if ($this->owner->ActionType != 'Email') {
            $this->owner->ActionEmail = null;
        }

        if ($this->owner->ActionType != 'Phone') {
            $this->owner->ActionPhone = null;
        }

        if ($this->owner->ActionType != 'Element') {
            $this->owner->ActionElementID = null;
        }
    }

    public function updateValidate(ValidationResult $result)
    {
        switch ($this->owner->ActionType) {
            case 'Internal':
                if (!$this->owner->ActionInternalLinkID) {
                    $result->addError('Internal Link is required');
                }

                break;
            case 'Anchor':
                if (trim($this->owner->ActionAnchorTarget) == '') {
                    $result->addError('Anchor Target is required');
                }

                break;
            case 'External':
                if (trim($this->owner->ActionExternalLink) == '') {
                    $result->addError('External Link is required');
                }

                break;
            case 'Email':
                if (trim($this->owner->ActionEmail) == '') {
                    $result->addError('Email is required');
                }

                break;
            case 'Phone':
                if (trim($this->owner->ActionPhone) == '') {
                    $result->addError('Phone Number is required');
                }

                break;
            case 'Element':
                if (is_null($this->owner->ActionElementID)) {
                    $result->addError('Element is required');
                }

                break;
        }
    }
}
