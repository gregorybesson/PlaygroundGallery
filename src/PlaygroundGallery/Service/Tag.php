<?php

namespace PlaygroundGallery\Service;

use PlaygroundGallery\Entity\Tag as TagEntity;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\NotEmpty;
use ZfcBase\EventManager\EventProvider;
use PlaygroundGallery\Options\ModuleOptions;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;
use Zend\Stdlib\ErrorHandler;

class Tag extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var tagMapper
     */
    protected $tagMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var tagForm
     */
    protected $tagForm;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;


    /**
     *
     * This service is ready for create a tag
     *
     * @param  array  $data
     * @param  string $formClass
     *
     * @return \PlaygroundGallery\Entity\Tag
     */
    public function create(array $data)
    {
        $tag = new TagEntity();
        $tag->populate($data);
        $entityManager = $this->getServiceManager()->get('playgroundgallery_doctrine_em');

        $form = $this->getTagForm();

        $this->addParent($tag, $data);

        $form->bind($tag);
        $form->setData($data);

        if (!$form->isValid()) {
            var_dump($form->getMessages());
            return false;
        }

        $tagMapper = $this->getTagMapper();
        $tag = $tagMapper->insert($tag);

        return $tag;
    }

    /**
     *
     * This service is ready for edit a tag
     *
     * @param  array  $data
     * @param  string $tag
     * @param  string $formClass
     *
     * @return \PlaygroundGallery\Entity\Tag
     */
    public function edit(array $data, $tag)
    {
        $entityManager = $this->getServiceManager()->get('playgroundgallery_doctrine_em');

        $form  = $this->getTagForm();

        $this->addParent($tag, $data);

        $form->bind($tag);

        $form->setData($data);
        $tag->populate($data);

        if (!$form->isValid()) {
            return false;
        }
        $tag = $this->getTagMapper()->update($tag);

        return $tag;
    }

    public function addParent($tag, $data)
    {
        if (empty($data['parent'])) {
            $tag->setParent(null);
            return $tag;
        }

        $parent = $this->getTagMapper()->findById($data['parent']);

        $tag->setParent($parent);
        return $this;
    }
    
    /**
     * setOptions
     * @param  ModuleOptions $options
     *
     * @return PlaygroundGallery\Service\Tag $this
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * getOptions
     *
     * @return ModuleOptions $optins
     */
    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceManager()->get('playgroundgallery_module_options'));
        }

        return $this->options;
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

    /**
     * getCategoryMapper
     *
     * @return CategoryMapper
     */
    public function getTagMapper()
    {
        if (null === $this->tagMapper) {
            $this->tagMapper = $this->getServiceManager()->get('playgroundgallery_tag_mapper');
        }

        return $this->tagMapper;
    }
    
    public function setTagMapper($tagMapper)
    {
        $this->tagMapper = $tagMapper;
        return $this;
    }

    /**
     * setCategoryMapper
     * @param  CategoryMapper $companyMapper
     *
     * @return PlaygroundGallery\Entity\Category Category
     */
    public function setCategoryMapper($tagMapper)
    {
        $this->tagMapper = $tagMapper;

        return $this;
    }

    /**
     * getTagForm
     *
     * @return tagForm
     */
    public function getTagForm()
    {
        if (null === $this->tagForm) {
            $this->tagForm = $this->getServiceManager()->get('playgroundgallery_tag_form');
        }

        return $this->tagForm;
    }

    /**
     * setTagForm
     * @param  PlaygroundGallery\Form\Tag $tagForm
     *
     * @return PlaygroundGallery\Service\Tag this
     */
    public function setTagForm($tagForm)
    {
        $this->tagForm = $tagForm;

        return $this;
    }
}
