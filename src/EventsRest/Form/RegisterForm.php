<?php
namespace EventsRest\Form;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

class RegisterForm extends Form
{


    public function init()
    {
        $hydrator = new ClassMethods(true);
        $this->setName('eventsrest');
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '/restful/register/save');
        $this->setHydrator($hydrator)->setObject(new \EventsRest\Entity\EventsRestfulCredentials());

        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type' => 'textarea',
                'class' => 'form-control',
                'placeholder' => _('Write here the description of your own website'),
                'rows' => 10
            ),
            'options' => array(
                'label' => _('Describe your website in brief here'),
            ),
            'filters' => array(
                array(
                    'name' => 'StringTrim'
                )
            )
        ));

        $this->add(array(
            'name' => 'url',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
                'placeholder' => _('Write here the endpoint of your own website (eg. http://www.mysite.com)'),
            ),
            'options' => array(
                'label' => _('Website URL'),
            ),
            'filters' => array(
                array(
                    'name' => 'StringTrim'
                )
            )
        ));

        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
                'placeholder' => _('Write here the email address of your technical administrator'),
            ),

            'options' => array(
                'label' => _('Technical Contact eMail'),
            ),
            'filters' => array(
                array(
                    'name' => 'StringTrim'
                )
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'class' => 'btn btn-success',
                'value' => _('Request your API Token')
            )
        ));
    }
}