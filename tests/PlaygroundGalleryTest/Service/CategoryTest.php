<?php
namespace PlaygroundGalleryTest\Service;

use PlaygroundGalleryTest\Bootstrap;
use \PlaygroundGallery\Entity\Category as CategoryEntity;

class CategoryTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;

    /**
     * Category sample
     * @var Array
     */
    protected $categoryData;

    public function setUp()
    {
        $this->categoryData = array(
            'name' => 'CeciEstUnTitre',
        );
        parent::setUp();
    }

    public function testCreateTrue()
    {
        $service = new \PlaygroundGallery\Service\Category();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $categoryPostUpdate = new CategoryEntity;
        $categoryPostUpdate->populate($this->categoryData);

        $mapper = $this->getMockBuilder('PlaygroundGallery\Mapper\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($categoryPostUpdate));
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($categoryPostUpdate));

        $form = $this->getMockBuilder('PlaygroundGallery\Form\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $form->expects($this->any())
            ->method('bind')
            ->will($this->returnValue(true));
        $form->expects($this->any())
            ->method('setData')
            ->will($this->returnValue(true));
        $form->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));

        $service->setCategoryForm($form);

        $service->setCategoryMapper($mapper);

        $this->categoryData['locales'] = array();

        $category = $service->create($this->categoryData);

        $this->assertEquals($this->categoryData['name'], $category->getName());
    }

    public function testEditTrue()
    {
        $service = new \PlaygroundGallery\Service\Category();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $category = new CategoryEntity;
        $category->populate($this->categoryData);

        $mapper = $this->getMockBuilder('PlaygroundGallery\Mapper\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($category));
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($category));

        $form = $this->getMockBuilder('PlaygroundGallery\Form\Media')
            ->disableOriginalConstructor()
            ->getMock();
        $form->expects($this->any())
            ->method('bind')
            ->will($this->returnValue(true));
        $form->expects($this->any())
            ->method('setData')
            ->will($this->returnValue(true));
        $form->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));

        $service->setCategoryForm($form);

        $service->setCategoryMapper($mapper);

        $category = $service->edit(array('name' => 'New one'), $category);

        $this->assertNotEquals($category->getName(), $this->categoryData['name']);
    }

    
}