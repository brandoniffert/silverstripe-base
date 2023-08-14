<?php

namespace App\Form\Field;

use SilverStripe\Forms\DropdownField;

class StateDropdownField extends DropdownField
{
    private static $default_states = [
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'DC' => 'District of Columbia',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
    ];

    protected $states;

    protected $extraClasses = ['dropdown'];

    public function __construct($name, $title = null, $source = [], $value = '', $form = null)
    {
        if (!empty($source)) {
            $this->setStates($source);
        }

        $this->setDisabledItems();

        parent::__construct($name, ($title === null) ? $name : $title, $source, $value, $form);
    }

    public function setStates($states = [])
    {
        if ($states !== (array)$states) {
            trigger_error(
                "The \$source passed isn't an array. When passing a source it must be an array.",
                E_USER_ERROR
            );
        }

        $globalDefaults = empty($states);

        if ($globalDefaults) {
            $states = $this->getDefaultStatesList();
        }

        reset($states);

        if ((int)key($states) === key($states)) {
            foreach ($states as $state) {
                $updatedSource[$state] = $state;
            }
        }

        $this->states = isset($updatedSource) ? $updatedSource : $states;

        return $this;
    }

    protected function getDefaultStatesList()
    {
        return $this->config()->get('default_states');
    }

    public function getStates()
    {
        if (!$this->states || empty($this->states)) {
            $this->setStates();
        }
        return $this->states;
    }

    public function setSource($source = [])
    {
        $this->setStates($source);
        return $this;
    }

    public function getSource()
    {
        return $this->getStates();
    }

    public function setDisabledItems($disabled = [''])
    {
        return parent::setDisabledItems($disabled);
    }

    public function validate($validator)
    {
        return true;
    }
}
