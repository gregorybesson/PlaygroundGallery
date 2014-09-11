<?php
namespace PlaygroundGalleryTest\Service;

use PlaygroundGalleryTest\Bootstrap;
use \PlaygroundGallery\Entity\Tag as TagEntity;

class TagTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;

    /**
     * Tag sample
     * @var Array
     */
    protected $tagData;

    public function setUp()
    {
        $this->tagData = array(
            'name' => 'Tag Name *ù£µ%',
        );
        parent::setUp();
    }

    public function testCreateTrue()
    {
        $service = new \PlaygroundGallery\Service\Tag();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $tagPostUpdate = new TagEntity();
        $tagPostUpdate->populate($this->tagData);

        $mapper = $this->getMockBuilder('PlaygroundGallery\Mapper\Tag')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($tagPostUpdate));
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($tagPostUpdate));

        $form = $this->getMockBuilder('PlaygroundGallery\Form\Tag')
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

        $service->setTagForm($form);

        $service->setTagMapper($mapper);

        $tag = $service->create($this->tagData);

        $this->assertEquals($this->tagData['name'], $tag->getName());
    }

    public function testEditTrue()
    {
        $service = new \PlaygroundGallery\Service\Tag();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $tag = new TagEntity;
        $tag->populate($this->tagData);

        $mapper = $this->getMockBuilder('PlaygroundGallery\Mapper\Tag')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($tag));
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($tag));

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

        $service->setTagForm($form);

        $service->setTagMapper($mapper);

        $tag = $service->edit(array('name' => 'New one'), $tag);

        $this->assertNotEquals($tag->getName(), $this->tagData['name']);
    }

    
}