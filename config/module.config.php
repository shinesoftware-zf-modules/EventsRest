<?php
return array(
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Route' => array(

                // https://github.com/zfcampus/zf-oauth2 module
                // http://www.itango.com/oauth/authorize?response_type=code&client_id=testclient&redirect_uri=/oauth/receivecode&state=xyz
                // http --auth testclient:testpass -f POST http://www.itango.com/oauth grant_type=authorization_code&code=E6F3FABD1C6EA4F5FD2485F5427AF8199144FEBE&redirect_uri=/oauth/receivecode
                array('route' => 'oauth', 'roles' => array('guest')),
                array('route' => 'oauth/authorize', 'roles' => array('guest')),
                array('route' => 'oauth/resource', 'roles' => array('guest')),
                array('route' => 'oauth/code', 'roles' => array('guest')),

                array('route' => 'register', 'roles' => array('user')),
                array('route' => 'register/default', 'roles' => array('user')),

                // Generic route guards
                array('route' => 'rest-v1', 'roles' => array('guest')),
                array('route' => 'rest-v1/events', 'roles' => array('guest')),
                array('route' => 'rest-v1/events/auth', 'roles' => array('guest')),
                array('route' => 'rest-v1', 'roles' => array('guest')),
                array('route' => 'rest-v1/category', 'roles' => array('guest')),
                array('route' => 'rest-v1/category/auth', 'roles' => array('guest')),
                array('route' => 'rest-v1/country', 'roles' => array('guest')),
                array('route' => 'rest-v1/country/auth', 'roles' => array('guest')),
                array('route' => 'rest-v1/language', 'roles' => array('guest')),
                array('route' => 'rest-v1/language/auth', 'roles' => array('guest')),
                array('route' => 'rest-v1/userevents', 'roles' => array('guest')),
                array('route' => 'rest-v1/userevents/auth', 'roles' => array('guest')),

                array('route' => 'zfcadmin/events-restful-credentials', 'roles' => array('admin')),
                array('route' => 'zfcadmin/events-restful-credentials/default', 'roles' => array('admin')),

            ),
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'EventsRest\Controller\Register' => 'EventsRest\Factory\RegisterControllerFactory',
            'EventsRest\Controller\Userevents' => 'EventsRest\Factory\UsereventsControllerFactory',
            'EventsRest\Controller\Language' => 'EventsRest\Factory\LanguageControllerFactory',
            'EventsRest\Controller\Country' => 'EventsRest\Factory\CountryControllerFactory',
            'EventsRest\Controller\Events' => 'EventsRest\Factory\EventsControllerFactory',
            'EventsRest\Controller\Category' => 'EventsRest\Factory\CategoryControllerFactory',
            'EventsRestAdmin\Controller\Index' => 'EventsRestAdmin\Factory\IndexControllerFactory',
        )
    ),

    'controller_plugins' => array(
        'invokables' => array(
            'BasicAuthPlugin' => 'EventsRest\Controller\Plugin\BasicAuthPlugin',
        )
    ),

    'navigation' => array(


        'admin' => array(
            'events' => array(
                'pages' => array(
                    array(
                        'label' => 'Restful Credentials',
                        'route' => 'zfcadmin/events-restful-credentials',
                        'icon' => 'fa fa-key'
                    ),
                ),
            ),
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'register' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/restful',
                    'defaults' => array(
                        '__NAMESPACE__' => 'EventsRest\Controller',
                        'controller' => 'Register',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(),
                        ),
                    ),
                ),
            ),
            'rest-v1' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/v1',
                    'defaults' => array(
                        'controller' => 'EventsRest\Controller\Events',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'events' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/events',
                            'defaults' => array(
                                'controller' => 'EventsRest\Controller\Events',
                            ),
                            'constraints' => array(
                                'secret' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'id' => '[0-9]+',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'auth' => array(
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route' => '/:secret[/:id]',
                                    'constraints' => array(
                                        'secret' => '[a-zA-Z0-9]+',
                                        'id' => '[0-9]+',
                                    ),
                                ),
                            )
                        )
                    ),
                    'category' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/category',
                            'defaults' => array(
                                'controller' => 'EventsRest\Controller\Category',
                            ),
                            'constraints' => array(
                                'secret' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'id' => '[0-9]+',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'auth' => array(
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route' => '/:secret[/:id]',
                                    'constraints' => array(
                                        'secret' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                        'id' => '[0-9]+',
                                    ),
                                ),
                            )
                        )
                    ),
                    'country' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/country',
                            'defaults' => array(
                                'controller' => 'EventsRest\Controller\Country',
                            ),
                            'constraints' => array(
                                'secret' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'id' => '[0-9]+',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'auth' => array(
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route' => '/:secret[/:id]',
                                    'constraints' => array(
                                        'secret' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                        'id' => '[0-9]+',
                                    ),
                                ),
                            )
                        )
                    ),
                    'language' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/language',
                            'defaults' => array(
                                'controller' => 'EventsRest\Controller\Language',
                            ),
                            'constraints' => array(
                                'secret' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'id' => '[0-9]+',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'auth' => array(
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route' => '/:secret[/:id]',
                                    'constraints' => array(
                                        'secret' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                        'id' => '[0-9]+',
                                    ),
                                ),
                            )
                        )
                    ),
                    'userevents' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/userevents',
                            'defaults' => array(
                                'controller' => 'EventsRest\Controller\Userevents',
                            ),
                            'constraints' => array(
                                'secret' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'id' => '[0-9]+',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'auth' => array(
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route' => '/:secret[/:id]',
                                    'constraints' => array(
                                        'secret' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                        'id' => '[0-9]+',
                                    ),
                                ),
                            )
                        )
                    )
                )
            ),

            'zfcadmin' => array(
                'child_routes' => array(
                    'events-restful-credentials' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/restfulcredentials',
                            'defaults' => array(
                                '__NAMESPACE__' => 'EventsRestAdmin\Controller',
                                'controller' => 'Index',
                                'action' => 'index'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'default' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/[:controller[/:action[/:id]]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'id' => '[0-9]*'
                                    ),
                                ),
                            ),

                            'settings' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/settings/[:action[/:id]]',
                                    'constraints' => array(
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'id' => '[0-9]*'
                                    ),
                                    'defaults' => array(
                                        'controller' => 'EventsSettings\Controller\Event',
                                        'action' => 'index',
                                    )
                                )
                            )
                        ),
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'oauth/authorize'    => __DIR__ . '/../view/zf-oauth/auth/authorize.phtml',
            'oauth/receive-code' => __DIR__ . '/../view/zf-oauth/auth/receive-code.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),

);
     