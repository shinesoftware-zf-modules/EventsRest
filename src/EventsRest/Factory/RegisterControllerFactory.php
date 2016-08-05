<?php
namespace EventsRest\Factory;

use EventsRest\Controller\RegisterController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegisterControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $restfulService = $realServiceLocator->get('RestUserService');
        $settings = $realServiceLocator->get('SettingsService');
        $mailservice = $realServiceLocator->get('MailService');
        $form = $realServiceLocator->get('FormElementManager')->get('EventsRest\Form\RegisterForm');
        $formfilter = $realServiceLocator->get('RegisterFilter');

        return new RegisterController($restfulService, $form, $formfilter, $mailservice, $settings);

    }
}