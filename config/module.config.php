<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'doctrine' => array(
        'driver' => array(
            'glaygroundgallery_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/PlaygroundGallery/Entity'
            ),
            
            'orm_default' => array(
                'drivers' => array(
                    'PlaygroundGallery\Entity'  => 'glaygroundgallery_entity'
                )
            )
        )
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'playgroundgallery' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/gallery',
                            'defaults' => array(
                                'controller' => 'PlaygroundGallery\Controller\Admin\GalleryAdmin',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'galleryPager' => array(
                                'type' => 'Segment',
                                'options' => array(
                                     'route' => '/gallery[/filters/:filters][/p/:p]',
                                    'defaults' => array(
                                        'controller' => 'PlaygroundGallery\Controller\Admin\GalleryAdmin',
                                        'action'     => 'index',
                                    ),
                                ),
                            ),
                            
                            'create' => array(
                                'type' => 'Segment',
                                'options' => array(
                                     'route' => '/create',
                                    'defaults' => array(
                                        'controller' => 'PlaygroundGallery\Controller\Admin\GalleryAdmin',
                                        'action'     => 'create',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/edit[/:mediaId]',
                                    'defaults' => array(
                                        'controller' => 'PlaygroundGallery\Controller\Admin\GalleryAdmin',
                                        'action'     => 'edit',
                                    ),
                                    'constraints' => array(
                                        'mediaId' => '[0-9]*',
                                    ),
                                ),
                            ),
                            'download' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/download[/:mediaId]',
                                    'defaults' => array(
                                        'controller' => 'PlaygroundGallery\Controller\Admin\GalleryAdmin',
                                        'action'     => 'download',
                                    ),
                                    'constraints' => array(
                                        'mediaId' => '[0-9]*',
                                    ),
                                ),
                            ),
                            'remove' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/remove[/:mediaId]',
                                    'defaults' => array(
                                        'controller' => 'PlaygroundGallery\Controller\Admin\GalleryAdmin',
                                        'action'     => 'remove',
                                    ),
                                    'constraints' => array(
                                        'mediaId' => '[0-9]*',
                                    ),
                                ),
                            ),
                            'category' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/category'
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'create' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                             'route' => '/create',
                                            'defaults' => array(
                                                'controller' => 'PlaygroundGallery\Controller\Admin\GalleryAdmin',
                                                'action'     => 'createCategory',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                             'route' => '/edit[/:categoryId]',
                                            'defaults' => array(
                                                'controller' => 'PlaygroundGallery\Controller\Admin\GalleryAdmin',
                                                'action'     => 'editCategory',
                                            ),
                                            'constraints' => array(
                                                'categoryId' => '[0-9]*',
                                            ),
                                        ),
                                    ),
                                    'remove' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                             'route' => '/remove[/:categoryId]',
                                            'defaults' => array(
                                                'controller' => 'PlaygroundGallery\Controller\Admin\GalleryAdmin',
                                                'action'     => 'removeCategory',
                                            ),
                                            'constraints' => array(
                                                'categoryId' => '[0-9]*',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'PlaygroundGallery\Controller\Admin\GalleryAdmin'  => 'PlaygroundGallery\Controller\Admin\GalleryAdminController',
        ),
    ),
    'navigation' => array(
        'admin' => array(
            'playgroundgallery' => array(
                'label' => 'Gallery',
                'route' => 'admin/playgroundgallery',
                'resource' => 'playgroundgallery',
                'privilege' => 'index',
            ),
        ),
    ),
    'autorize_user' => true,
);