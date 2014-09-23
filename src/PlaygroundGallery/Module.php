<?php

namespace PlaygroundGallery;

use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{
    protected $eventsArray = array();
    
    public function onBootstrap(MvcEvent $e)
    {
        $application     = $e->getTarget();
        $serviceManager  = $application->getServiceManager();
        $eventManager    = $application->getEventManager();

        $translator = $serviceManager->get('translator');

        // Gestion de la locale
        if (PHP_SAPI !== 'cli') {
            $locale = null;
            $options = $serviceManager->get('playgroundcore_module_options');

            $locale = $options->getLocale();

            $translator->setLocale($locale);

            // plugins
            $translate = $serviceManager->get('viewhelpermanager')->get('translate');
            $translate->getTranslator()->setLocale($locale);  
        }
        
        AbstractValidator::setDefaultTranslator($translator,'playgroundgallery');
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'aliases' => array(
                'playgroundgallery_doctrine_em' => 'doctrine.entitymanager.orm_default',
            ),
            'factories' => array(
                'playgroundgallery_module_options' => function  ($sm) {
                    $config = $sm->get('Configuration');
                    
                    return new Options\ModuleOptions(isset($config['playgroundgallery']) ? $config['playgroundgallery'] : array());
                },
                
                'playgroundgallery_category_form' => function  ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Category(null, $sm, $translator);
                    
                    return $form;
                },

                'playgroundgallery_media_form' => function  ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Media(null, $sm, $translator);
                    
                    return $form;
                },
                
                'playgroundgallery_tag_form' => function  ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Tag(null, $sm, $translator);
                
                    return $form;
                },

                'playgroundgallery_category_mapper' => function  ($sm) {
                    return new Mapper\Category($sm->get('playgroundgallery_doctrine_em'), $sm->get('playgroundgallery_module_options'));
                },

                'playgroundgallery_media_mapper' => function  ($sm) {
                    return new Mapper\Media($sm->get('playgroundgallery_doctrine_em'), $sm->get('playgroundgallery_module_options'));
                },
                
                'playgroundgallery_tag_mapper' => function  ($sm) {
                    return new Mapper\Tag($sm->get('playgroundgallery_doctrine_em'), $sm->get('playgroundgallery_module_options'));
                },
            ),
            'invokables' => array(
                'playgroundgallery_category_service' => 'PlaygroundGallery\Service\Category',
                'playgroundgallery_media_service' => 'PlaygroundGallery\Service\Media',
                'playgroundgallery_tag_service' => 'PlaygroundGallery\Service\Tag',
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'printCategoryTree' => function ($sm) {
                    $viewHelper = new View\Helper\PrintCategoryTree();
                    $viewHelper->setCategoryService($sm->getServiceLocator()->get('playgroundgallery_category_service'));
                    return $viewHelper;
                },
                'printTagTree' => function ($sm) {
                    $viewHelper = new View\Helper\PrintTagTree();
                    $viewHelper->setTagService($sm->getServiceLocator()->get('playgroundgallery_tag_service'));
                    return $viewHelper;
                },
                'getTags' => function ($sm) {
                    $viewHelper = new View\Helper\GetTags();
                    $viewHelper->setTagService($sm->getServiceLocator()->get('playgroundgallery_tag_service'));
                    return $viewHelper;
                },
            ),
        );
    }
}
