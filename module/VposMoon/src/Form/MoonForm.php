<?php

namespace VposMoon\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

/**
 * This form is used to collect user's login, password and 'Remember Me' flag.
 */
class MoonForm extends Form
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('moon-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        // Add "email" field
        $this->add(
            [
                'type' => 'textarea',
                'name' => 'scan',
                'options' => [
                    'label' => 'Your scan please',
                ],
            ]
        );

        // Add the Submit button
        $this->add(
            [
                'type' => 'submit',
                'name' => 'submit',
                'attributes' => [
                    'value' => 'Post data',
                    'id' => 'submit',
                ],
            ]
        );
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        // Add input for "email" field
        $inputFilter->add(
            [
                'name' => 'scan',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Laminas\Validator\NotEmpty::IS_EMPTY => 'No input - No fun!',
                            ),
                        ),
                    ),
                ),
            ]
        );
    }

}
