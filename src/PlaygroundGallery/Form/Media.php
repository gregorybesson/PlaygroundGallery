<?php

namespace PlaygroundGallery\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceManager;
use PlaygroundGallery\Entity\Media as MediaEntity;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Media extends ProvidesEventsForm
{

    protected $serviceManager;
    protected $categoryService;

    public function __construct ($name = null, ServiceManager $sm, Translator $translator)
    {

        parent::__construct($name);
        $this->setServiceManager($sm);
        
        $this->setAttribute('enctype','multipart/form-data');
        
        $this->add(array(
            'name' => 'category',
            'options' => array(
                'label' => $translator->translate('Category', 'playgroundgallery'),
            ),
            'attributes' => array(
                'type' => 'text',
            	'placeholder' => $translator->translate('Category', 'playgroundgallery'),
            	'required' => 'required',
                'class' => 'form-control'
            ),
            'validator' => array(
                new \Zend\Validator\NotEmpty(),
            )
        ));

        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => $translator->translate('Name', 'playgroundgallery'),
            ),
            'attributes' => array(
                'type' => 'text',
            	'placeholder' => $translator->translate('Name', 'playgroundgallery'),
            	'required' => 'required',
                'class' => 'form-control'
            ),
            'validator' => array(
                new \Zend\Validator\NotEmpty(),
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'upload_or_paste',
            'options' => array(
                'label' => $translator->translate('Upload image or paste url', 'playgroundgallery'),
                'value_options' => array(
                    'paste_url'      => $translator->translate('Past url', 'playgroundgallery'),
                    'upload'        => $translator->translate('Upload image', 'playgroundgallery'),
                ),
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        
        $this->add(array(
            'name' => 'uploadImage',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => $translator->translate('Image', 'playgroundgallery'),
            ),
        ));
        
        $this->add(array(
    		'name' => 'url',
    		'options' => array(
    				'label' => $translator->translate('Url', 'playgroundgallery'),
    		),
    		'attributes' => array(
    				'type' => 'text',
    				'placeholder' => $translator->translate('Url', 'playgroundgallery'),
    				'required' => 'required',
                    'class' => 'form-control'
            ),
            'validator' => array(
                new \Zend\Validator\NotEmpty(),
            )
        ));

        $this->add(array(
            'name' => 'poster',
            'options' => array(
                    'label' => $translator->translate('Poster', 'playgroundgallery'),
            ),
            'attributes' => array(
                    'type' => 'text',
                    'placeholder' => $translator->translate('Poster', 'playgroundgallery'),
                    'required' => 'required',
                    'class' => 'form-control'
            ),
            'validator' => array(
                new \Zend\Validator\NotEmpty(),
            )
        ));

        $this->add(array(
    		'name' => 'credit',
    		'options' => array(
    				'label' => $translator->translate('Credit', 'playgroundgallery'),
    		),
    		'attributes' => array(
    				'type' => 'text',
    				'placeholder' => $translator->translate('Credit', 'playgroundgallery'),
    				'required' => 'required',
                    'class' => 'form-control'
            ),
            'validator' => array(
                new \Zend\Validator\NotEmpty(),
            )
        ));

        $this->add(array(
    		'name' => 'description',
    		'options' => array(
    				'label' => $translator->translate('Description', 'playgroundgallery'),
    		),
    		'attributes' => array(
    				'type' => 'Zend\Form\Element\Textarea',
    				'placeholder' => $translator->translate('Description', 'playgroundgallery'),
    				'required' => 'required',
                    'class' => 'form-control'
            ),
            'validator' => array(
                new \Zend\Validator\NotEmpty(),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'category',
            'options' => array(
                'label' => $translator->translate('Category', 'playgroundtranslate'),
                'value_options' => $this->getCategories(),
                'default' => 3
            ),
            'attributes' => array(
                'class' => 'form-control'
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'tags',
            'options' => array(
                'label' => $translator->translate('tags', 'playgroundgallery'),
                'value_options' => $this->getTags(),
                'disable_inarray_validator' => true
            ),
            'attributes' => array(
                'class' => 'form-control multiselect',
                'multiple' => 'multiple',
                'value' => array_keys($this->getTags()),
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
     * Récupère la liste des tags
     *
     * @return array
     */
    private function getTags()
    {
        $tags = $this->getServiceManager()->get('playgroundgallery_tag_service')->getTagMapper()->findAll();
        $tagForm = array();
        foreach ($tags as $tag) {
            $tagForm[$tag->getId()] = $tag->getName();
        }
        return $tagForm;
    }

    private function getCategories() {
        $categories = $this->getCategoryService()->getCategoryMapper()->findBy(array('parent' => null));
        $categoriesForm = array();
        foreach ($categories as $category) {
            $this->getChildrenCategories($category, $categoriesForm);
        }
        return $categoriesForm;
    }

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
    public function getServiceManager ()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager (ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    /**
     * Retrieve category service instance
     *
     * @return CategoryService
     */
    public function getCategoryService ()
    {
        if (null === $this->categoryService) {
            $this->categoryService = $this->getServiceManager()->get('playgroundgallery_category_service');
        }

        return $this->categoryService;
    }

    /**
     * Set service category instance
     *
     * @param  ServiceManager $categoryService
     * @return this
     */
    public function setCategoryService ($categoryService)
    {
        $this->categoryService = $categoryService;

        return $this;
    }

}
