<?php
namespace EventsRestAdmin\Factory;

use EventsRestAdmin\Controller\IndexController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use EventsRestAdmin\Model\EventDatagrid;

class IndexControllerFactory implements FactoryInterface
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
        $service = $realServiceLocator->get('RestUserService');
        $settings = $realServiceLocator->get('SettingsService');
        $dbAdapter = $realServiceLocator->get('Zend\Db\Adapter\Adapter');
        $datagrid = $realServiceLocator->get('ZfcDatagrid\Datagrid');
        $form = $realServiceLocator->get('FormElementManager')->get('EventsRest\Form\EventsRestfulCredentialsForm');
        $formfilter = $realServiceLocator->get('EventsRestfulCredentialFilter');

        // prepare the datagrid to handle the custom columns and data
        $theDatagrid = new EventDatagrid($dbAdapter, $datagrid, $settings);
        $grid = $theDatagrid->getDatagrid();

        return new IndexController($service, $form, $formfilter, $grid, $settings);
    }
}