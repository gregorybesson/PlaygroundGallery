<?php

namespace PlaygroundGalleryTest\Service;

use PlaygroundGalleryTest\Bootstrap;
use \PlaygroundGallery\Entity\Media as MediaEntity;
use \PlaygroundGallery\Entity\Category as CategoryEntity;

class MediaTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;

    /**
     * Media sample
     * @var Array
     */
    protected $mediaData;

    public function setUp()
    {
        $this->mediaData = array(
            'name' => 'CeciEstUnTitre',
            'credit' => 'CeciEstUnCredit',
            'url' => 'http://lorempixel.com/400/600/sports/2/',
            'description' => 'CeciEstUneDescription',
            'category' => 1,
        );
        parent::setUp();
    }

    public function testCreateTrue()
    {
        $service = new \PlaygroundGallery\Service\Media();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $mediaPostUpdate = new MediaEntity;
        $mediaPostUpdate->populate($this->mediaData);

        $mapper = $this->getMockBuilder('PlaygroundGallery\Mapper\Media')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($mediaPostUpdate));
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($mediaPostUpdate));


        $category = new CategoryEntity;
        $category->populate(array('name'=>'test'));
        $mapperCate = $this->getMockBuilder('PlaygroundGallery\Mapper\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $mapperCate->expects($this->any())
            ->method('findBy')
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

        $service->setMediaForm($form);

        $service->setMediaMapper($mapper);
        $service->setCategoryMapper($mapperCate);

        $media = $service->create($this->mediaData);

        $this->assertEquals($this->mediaData['name'], $media->getName());
    }

    public function testCreateFalse()
    {
        $service = new \PlaygroundGallery\Service\Media();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $mediaPostUpdate = new MediaEntity;
        $mediaPostUpdate->setName($this->mediaData['name']);

        $mapper = $this->getMockBuilder('PlaygroundGallery\Mapper\Media')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($mediaPostUpdate));
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($mediaPostUpdate));

        $category = new CategoryEntity;
        $category->populate(array('name'=>'test'));
        $mapperCate = $this->getMockBuilder('PlaygroundGallery\Mapper\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $mapperCate->expects($this->any())
            ->method('findBy')
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
            ->will($this->returnValue(false));

        $service->setMediaForm($form);

        $service->setMediaMapper($mapper);
        $service->setCategoryMapper($mapperCate);

        $media = $service->create($this->mediaData);

        $this->assertFalse($media);
    }

    public function testEditTrue()
    {
        $service = new \PlaygroundGallery\Service\Media();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $mediaPostUpdate = new MediaEntity;
        $mediaPostUpdate->populate($this->mediaData);

        $mapper = $this->getMockBuilder('PlaygroundGallery\Mapper\Media')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($mediaPostUpdate));
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($mediaPostUpdate));

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

        $service->setMediaForm($form);

        $service->setMediaMapper($mapper);

        $media = $service->edit(array('name' => 'New one'), $mediaPostUpdate);

        $this->assertNotEquals($media->getName(), $this->mediaData['name']);
    }

    public function testEditFalse()
    {
        $service = new \PlaygroundGallery\Service\Media();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $mediaPostUpdate = new MediaEntity;
        $mediaPostUpdate->populate($this->mediaData);

        $mapper = $this->getMockBuilder('PlaygroundGallery\Mapper\Media')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($mediaPostUpdate));
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($mediaPostUpdate));

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
            ->will($this->returnValue(false));

        $service->setMediaForm($form);

        $service->setMediaMapper($mapper);

        $media = $service->edit(array('name' => 'New one'), $mediaPostUpdate);

        $this->assertFalse($media);
    }
}