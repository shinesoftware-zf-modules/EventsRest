<?php
namespace EventsRest\Factory;

use EventsRest\Controller\UsereventsController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserEventsControllerFactory implements FactoryInterface
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
        $eventService = $realServiceLocator->get('EventService');
        $restfulService = $realServiceLocator->get('RestUserService');
        $eventSettings = $realServiceLocator->get('SettingsService');

        return new UsereventsController($restfulService, $eventService, $eventSettings);

    }
}