<?php
namespace EventsRest\Factory;

use EventsRest\Controller\EventsController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventsControllerFactory implements FactoryInterface
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
        $form = $realServiceLocator->get('FormElementManager')->get('Events\Form\EventForm');
        $formfilter = $realServiceLocator->get('EventFilter');
        
        return new EventsController($restfulService, $eventService, $form, $formfilter, $eventSettings);
        
    }
}