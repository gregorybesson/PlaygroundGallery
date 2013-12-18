<?php

namespace PlaygroundGallery\Service;

use PlaygroundGallery\Entity\Media as MediaEntity;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\NotEmpty;
use ZfcBase\EventManager\EventProvider;
use PlaygroundGallery\Options\ModuleOptions;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;
use Zend\Stdlib\ErrorHandler;

class Media extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var mediaMapper
     */
    protected $mediaMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var mediaForm
     */
    protected $mediaForm;

    /**
     * @var categoryMapper
     */
    protected $categoryMapper;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;


    /**
     *
     * This service is ready for create a media
     *
     * @param  array  $data
     * @param  string $formClass
     *
     * @return \PlaygroundGallery\Entity\Media
     */
    public function create(array $data)
    {
        $media = new MediaEntity();
        $media->populate($data);
        $entityManager = $this->getServiceManager()->get('playgroundgallery_doctrine_em');

        $form = $this->getMediaForm();

        $this->addCategory($media, $data);

        $form->bind($media);
        $form->setData($data);

        if (!$form->isValid()) {
            return false;
        }

        $mediaMapper = $this->getMediaMapper();
        $media = $mediaMapper->insert($media);

        return $media;
    }

    /**
     *
     * This service is ready for edit a media
     *
     * @param  array  $data
     * @param  string $media
     * @param  string $formClass
     *
     * @return \PlaygroundGallery\Entity\Media
     */
    public function edit(array $data, $media)
    {
        $entityManager = $this->getServiceManager()->get('playgroundgallery_doctrine_em');

        $form  = $this->getMediaForm();

        $this->addCategory($media, $data);

        $form->bind($media);

        $form->setData($data);
        $media->populate($data);

        if (!$form->isValid()) {
            return false;
        }
        $media = $this->getMediaMapper()->update($media);

        return $media;
    }

    public function addCategory($media, $data)
    {
        if (empty($data['category'])) {
            return $media;
        }

        $category = $this->getCategoryMapper()->findById($data['category']);
        $media->setCategory($category);

        return $media;
    }
    
    /**
     * getMediaMapper
     *
     * @return MediaMapper
     */
    public function getMediaMapper()
    {
        if (null === $this->mediaMapper) {
            $this->mediaMapper = $this->getServiceManager()->get('playgroundgallery_media_mapper');
        }

        return $this->mediaMapper;
    }

    /**
     * setCompanyMapper
     * @param  MediaMapper $companyMapper
     *
     * @return PlaygroundGallery\Entity\Media Media
     */
    public function setMediaMapper($mediaMapper)
    {
        $this->mediaMapper = $mediaMapper;

        return $this;
    }

    /**
     * setOptions
     * @param  ModuleOptions $options
     *
     * @return PlaygroundGallery\Service\Media $this
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
    public function getCategoryMapper()
    {
        if (null === $this->categoryMapper) {
            $this->categoryMapper = $this->getServiceManager()->get('playgroundgallery_category_mapper');
        }

        return $this->categoryMapper;
    }

    /**
     * setCategoryMapper
     * @param  CategoryMapper $companyMapper
     *
     * @return PlaygroundGallery\Entity\Category Category
     */
    public function setCategoryMapper($categoryMapper)
    {
        $this->categoryMapper = $categoryMappers;

        return $this;
    }

    /**
     * getMediaForm
     *
     * @return mediaForm
     */
    public function getMediaForm()
    {
        if (null === $this->mediaForm) {
            $this->mediaForm = $this->getServiceManager()->get('playgroundgallery_media_form');
        }

        return $this->mediaForm;
    }

    /**
     * setMediaForm
     * @param  PlaygroundGallery\Form\Media $mediaForm
     *
     * @return PlaygroundGallery\Service\Media this
     */
    public function setMediaForm($mediaForm)
    {
        $this->mediaForm = $mediaForm;

        return $this;
    }
}