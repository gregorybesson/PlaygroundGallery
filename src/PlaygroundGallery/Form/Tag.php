<?php

namespace PlaygroundGallery\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceManager;

class Tag extends ProvidesEventsForm
{

    protected $serviceManager;

    public function __construct ($name = null, ServiceManager $sm, Translator $translator)
    {
        parent::__construct($name);
        $this->setServiceManager($sm);
        
        $this->add(array(
            'name' => 'tag',
            'options' => array(
                'label' => $translator->translate('Tag', 'playgroundgallery'),
            ),
            'attributes' => array(
                'type' => 'text',
            	'placeholder' => $translator->translate('Tag', 'playgroundgallery'),
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
            'name' => 'parent',
            'options' => array(
                'label' => $translator->translate('parent', 'playgroundgallery'),
                'value_options' => $this->getRootTags(),
            ),
            'attributes' => array(
                'class' => 'form-control',
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
    * @return array  liste des websites
    */
    private function getRootTags()
    {
        $translator = $this->getServiceManager()->get('translator');
        $tags = $this->getServiceManager()->get('playgroundgallery_tag_service')->getTagMapper()->findBy(array('parent' => null));
        $tagsForm = array('' => $translator->translate('--- ROOT ---'));
        foreach ($tags as $tag) {
           $tagsForm[$tag->getId()] = $tag->getName();
        }
        return $tagsForm;
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

}
