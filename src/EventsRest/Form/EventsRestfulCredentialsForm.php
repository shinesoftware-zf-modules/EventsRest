<?php
namespace EventsRest\Form;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

class EventsRestfulCredentialsForm extends Form
{

    public function init ()
    {
        $hydrator = new ClassMethods(true);
        $this->setName('eventsrest');
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '/admin/restfulcredentials/index/process');
        $this->setHydrator($hydrator)->setObject(new \EventsRest\Entity\EventsRestfulCredentials());


        $this->add(array (
            'type' => 'Profile\Form\Element\Profiles',
            'name' => 'user_id',
            'attributes' => array (
                'class' => 'form-control'
            ),
            'options' => array (
                'label' => 'Client ID',
            )
        ));


        $this->add(array (
            'name' => 'secret',
            'attributes' => array (
                'type' => 'text',
                'class' => 'form-control',
                'placeholder' => _('If this is empty, a new client-secret will be created'),
            ),
            'options' => array (
                'label' => 'Client Secret',
            ),
            'filters' => array (
                array (
                    'name' => 'StringTrim'
                )
            )
        ));

        $this->add(array (
            'name' => 'requests',
            'attributes' => array (
                'type' => 'text',
                'class' => 'form-control',
            ),
            'options' => array (
                'label' => _('Client requests'),
            ),
            'filters' => array (
                array (
                    'name' => 'StringTrim'
                )
            )
        ));

        $this->add(array (
            'name' => 'description',
            'attributes' => array (
                'type' => 'textarea',
                'class' => 'form-control',
            ),
            'options' => array (
                'label' => _('Description'),
            ),
            'filters' => array (
                array (
                    'name' => 'StringTrim'
                )
            )
        ));

        $this->add(array (
            'name' => 'url',
            'attributes' => array (
                'type' => 'text',
                'class' => 'form-control',
            ),
            'options' => array (
                'label' => _('Website URL'),
            ),
            'filters' => array (
                array (
                    'name' => 'StringTrim'
                )
            )
        ));

        $this->add(array (
            'name' => 'email',
            'attributes' => array (
                'type' => 'text',
                'class' => 'form-control',
            ),
            'options' => array (
                'label' => _('Contact email'),
            ),
            'filters' => array (
                array (
                    'name' => 'StringTrim'
                )
            )
        ));

        $this->add(array (
            'type' => 'Base\Form\Element\YesNo',
            'name' => 'insert',
            'attributes' => array (
                'class' => 'form-control'
            ),
            'options' => array (
                'label' => _('User can insert a new event'),
            )
        ));


        $this->add(array (
            'type' => 'Base\Form\Element\YesNo',
            'name' => 'Delete',
            'attributes' => array (
                'class' => 'form-control'
            ),
            'options' => array (
                'label' => _('User can delete an event'),
            )
        ));

        $this->add(array (
            'type' => 'Base\Form\Element\YesNo',
            'name' => 'list',
            'attributes' => array (
                'class' => 'form-control'
            ),
            'options' => array (
                'label' => _('User can see the list of the events'),
            )
        ));


        $this->add(array (
            'type' => 'Base\Form\Element\YesNo',
            'name' => 'enabled',
            'attributes' => array (
                'class' => 'form-control'
            ),
            'options' => array (
                'label' => _('Enabled'),
            )
        ));


        $this->add(array ( 
                'name' => 'submit', 
                'attributes' => array ( 
                        'type' => 'submit', 
                        'class' => 'btn btn-success',
                    'value' => _('Save')
                )
        ));
        $this->add(array (
                'name' => 'id',
                'attributes' => array (
                        'type' => 'hidden'
                )
        ));
    }
}