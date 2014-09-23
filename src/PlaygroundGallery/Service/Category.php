<?php

namespace PlaygroundGallery\Service;

use PlaygroundGallery\Entity\Category as CategoryEntity;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\NotEmpty;
use ZfcBase\EventManager\EventProvider;
use PlaygroundGallery\Options\ModuleOptions;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;
use Zend\Stdlib\ErrorHandler;

class Category extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var categoryMapper
     */
    protected $categoryMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var categoryForm
     */
    protected $categoryForm;

    /**
     *
     * This service is ready for create a category
     *
     * @param  array  $data
     * @param  string $formClass
     *
     * @return \PlaygroundGallery\Entity\Category
     */
    public function create(array $data)
    {
        $category = new CategoryEntity();
        $category->populate($data);
        $entityManager = $this->getServiceManager()->get('playgroundgallery_doctrine_em');

        $form = $this->getCategoryForm();

        $this->addCategoryParent($category, $data);

        if(array_key_exists('locales', $data)) {
            $category = $this->addLocale($category, $data['locales']);
        }

        $form->bind($category);
        $form->setData($data);



        $category = $this->getCategoryMapper()->insert($category);

        return $category;
    }

    /**
     *
     * This service is ready for edit a category
     *
     * @param  array  $data
     * @param  string $category
     * @param  string $formClass
     *
     * @return \PlaygroundGallery\Entity\Category
     */
    public function edit(array $data, $category)
    {
        $entityManager = $this->getServiceManager()->get('playgroundgallery_doctrine_em');

        $form  = $this->getCategoryForm();

        $this->addCategoryParent($category, $data);

        $form->bind($category);

        $form->setData($data);

        $category->setName($data['name']);

     
        $category = $this->getCategoryMapper()->update($category);

        return $category;
    }

    public function addCategoryParent($category, $data)
    {
        if (empty($data['parent'])) {
            return $category;
        }

        $categoryParent = $this->getCategoryMapper()->findById($data['parent']);
        $category->setParent($categoryParent);

        return $category;
    }

    public function addLocale($category, $data)
    {
        foreach ($data as $localeId) {
            $locale = $this->getServiceManager()->get('playgroundcore_locale_mapper')->findById($localeId);
            if($locale) {
                $category->addLocale($locale);
            }
        }
        return $category;
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
     * setCompanyMapper
     * @param  CategoryMapper $companyMapper
     *
     * @return PlaygroundGallery\Entity\Category Category
     */
    public function setCategoryMapper($categoryMapper)
    {
        $this->categoryMapper = $categoryMapper;

        return $this;
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
     * getCategoryForm
     *
     * @return categoryForm
     */
    public function getCategoryForm()
    {
        if (null === $this->categoryForm) {
            $this->categoryForm = $this->getServiceManager()->get('playgroundgallery_category_form');
        }

        return $this->categoryForm;
    }

    /**
     * setCategoryForm
     * @param  PlaygroundGallery\Form\Category $categoryForm
     *
     * @return PlaygroundGallery\Service\Category this
     */
    public function setCategoryForm($categoryForm)
    {
        $this->categoryForm = $categoryForm;

        return $this;
    }
}