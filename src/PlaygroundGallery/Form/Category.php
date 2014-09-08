<?php

namespace PlaygroundGallery\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceManager;

class Category extends ProvidesEventsForm
{

    /**
    * @var $serviceManager Service Manager
    */
    protected $serviceManager;

    public function __construct ($name = null, ServiceManager $sm, Translator $translator)
    {

        parent::__construct($name);
        $this->setServiceManager($sm);
        
        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => $translator->translate('Name', 'playgroundgallery'),
            ),
            'attributes' => array(
                'type' => 'text',
            	'placeholder' => $translator->translate('Name', 'playgroundgallery'),
            	'required' => 'required',
                'class' => 'form-control',
            ),
            'validator' => array(
                new \Zend\Validator\NotEmpty(),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'parent',
            'options' => array(
                'label' => $translator->translate('parent', 'playgroundgallery'),
                'value_options' => $this->getCategories(),
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));


        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'websites',
            'options' => array(
                'label' => $translator->translate('websites', 'playgroundgallery'),
                'value_options' => $this->getWebsites(),
            ),
            'attributes' => array(
                'class' => 'form-control multiselect',
                'multiple' => 'multiple',
                'value' => array_keys($this->getWebsites()),
            ),
        ));
        
        $submitElement = new Element\Button('submit');
        $submitElement->setLabel($translator->translate('Ok', 'playgroundgallery'))
            ->setAttributes(array(
            'type' => 'submit',
            'class'=> 'btn btn-success'
        ));

        $this->add($submitElement, array(
            //'priority' => - 100
        ));

    }

    /**
    * Récupère la liste des websites
    *
    * @return array $websitesForm liste des websites
    */
    private function getWebsites()
    {
        $websites = $this->getServiceManager()->get('playgroundcore_website_service')->getWebsiteMapper()->findAll();
        $websitesForm = array();
        foreach ($websites as $website) {
           $websitesForm[$website->getId()] = $website->getName();
        }
        return $websitesForm;
    }

    /**
    * Récupère la liste des categories
    *
    * @return array $websitesForm liste des categories
    */
    private function getCategories() {
        $categories = $this->getServiceManager()->get('playgroundgallery_category_service')->getCategoryMapper()->findBy(array('parent' => null));
        $categoriesForm = array();
        foreach ($categories as $category) {
            $this->getChildrenCategories($category, $categoriesForm);
        }
        return $categoriesForm;
    }

    /**
    * Récupère la liste des categories enfants d'une categorie
    *
    */
    private function getChildrenCategories($category, &$categoriesForm, $wave = 1) {
        
        $prefixe = '';
        for ($i=0; $i < $wave; $i++) { 
            $prefixe .= '-';
        }
        
        $categoriesForm[$category->getId()] = $prefixe.' '.$category->getName();
        foreach ($category->getChildren() as $category) {
            $this->getChildrenCategories($category, $categoriesForm, $wave+1);
        }
    }

     /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

}
