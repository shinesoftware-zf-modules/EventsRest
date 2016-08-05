<?php
namespace EventsRest\Form;

use Zend\InputFilter\InputFilter;

class RegisterFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'description',
            'required' => true
        ));

        $this->add(array(
            'name' => 'url',
            'required' => true
        ));

        $this->add(array(
            'name' => 'email',
            'required' => true
        ));

    }
}