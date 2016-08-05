<?php
namespace EventsRest\Form;
use Zend\InputFilter\InputFilter;

class EventsRestfulCredentialsFilter extends InputFilter
{

    public function __construct ()
    {
		$this->add(array (
			'name' => 'user_id',
			'required' => true
		));

		$this->add(array (
			'name' => 'secret',
			'required' => false
		));

		$this->add(array (
			'name' => 'url',
			'required' => true
		));

		$this->add(array (
			'name' => 'email',
			'required' => true
		));

    }
}