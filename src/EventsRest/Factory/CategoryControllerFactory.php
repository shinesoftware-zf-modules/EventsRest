<?php
namespace EventsRest\Factory;

use EventsRest\Controller\CategoryController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CategoryControllerFactory implements FactoryInterface
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
        $categoryService = $realServiceLocator->get('EventCategoryService');
        $userService = $realServiceLocator->get('RestUserService');

        $eventSettings = $realServiceLocator->get('SettingsService');

        return new CategoryController($userService, $categoryService, $eventSettings);
        
    }
}