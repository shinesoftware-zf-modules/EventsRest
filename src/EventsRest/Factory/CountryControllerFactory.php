<?php
namespace EventsRest\Factory;

use EventsRest\Controller\CountryController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CountryControllerFactory implements FactoryInterface
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
        $countryService = $realServiceLocator->get('CountryService');
        $userService = $realServiceLocator->get('RestUserService');
        $eventSettings = $realServiceLocator->get('SettingsService');

        return new CountryController($userService, $countryService, $eventSettings);
        
    }
}