<?php
namespace EventsRest\Factory;

use EventsRest\Controller\LanguageController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LanguageControllerFactory implements FactoryInterface
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
        $languageService = $realServiceLocator->get('LanguagesService');
        $userService = $realServiceLocator->get('RestUserService');
        $eventSettings = $realServiceLocator->get('SettingsService');

        return new LanguageController($userService, $languageService, $eventSettings);
        
    }
}