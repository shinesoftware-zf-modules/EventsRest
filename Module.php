<?php
namespace EventsRest;
use \Zend\Mvc\MvcEvent,
    \Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    \Zend\ModuleManager\Feature\ConfigProviderInterface;

use EventsRest\Entity\EventsRestfulCredentials;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Set the Services Manager items
     */
    public function getServiceConfig ()
    {
        return array(
            'factories' => array(

                'RestUserService' => function  ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $translator = $sm->get('translator');
                    $settings = $sm->get('SettingsService');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EventsRestfulCredentials());
                    $tableGateway = new TableGateway('events_restful_credentials', $dbAdapter, null, $resultSetPrototype);
                    $service = new \EventsRest\Service\UserService($dbAdapter, $tableGateway, $translator, $settings);
                    return $service;
                },

                'RegisterForm' => function ($sm) {
                    $form = new \EventsRest\Form\RegisterForm();
                    $form->setInputFilter($sm->get('RegisterFilter'));
                    return $form;
                },
                'RegisterFilter' => function ($sm) {
                    return new \EventsRest\Form\RegisterFilter();
                },

                'EventsRestfulCredentialForm' => function  ($sm)
                {
                    $form = new \EventsRest\Form\EventsRestfulCredentialsForm();
                    $form->setInputFilter($sm->get('EventsRestfulCredentialFilter'));
                    return $form;
                },
                'EventsRestfulCredentialFilter' => function  ($sm)
                {
                    return new \EventsRest\Form\EventsRestfulCredentialsFilter();
                },

            ),
        );
    }

    /**
     * Check the dependency of the module
     * (non-PHPdoc)
     * @see Zend\ModuleManager\Feature.DependencyIndicatorInterface::getModuleDependencies()
     */
    public function getModuleDependencies()
    {
        return array('Base', 'Events', 'Profile');
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    __NAMESPACE__ . "Admin" => __DIR__ . '/src/' . __NAMESPACE__ . "Admin",
                    __NAMESPACE__ . "Settings" => __DIR__ . '/src/' . __NAMESPACE__ . "Settings",
                ),
            ),
        );
    }

}