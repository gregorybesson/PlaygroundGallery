<?php

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Stdlib\Parameters;
use PlaygroundGallery\Entity\Tag as TagEntity;

class GalleryAdminControllerTest extends AbstractHttpControllerTestCase
{
    
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../TestConfig.php'
        );
        parent::setUp();
    }
    
    public function testCreateActionCanBeAccessed()
    {
        $this->dispatch('/admin/gallery/tag/create');
        $this->assertResponseStatusCode(302);
        
        $this->getRequest()
            ->setMethod('POST')
            ->setPost(new Parameters(array(
                'name' => 'Tag Name !:,*ù$'
        )));
        
        $this->dispatch('/admin/gallery/tag/create');
        $this->assertResponseStatusCode(302);
    
        $this->assertModuleName('playgroundgallery');
        $this->assertControllerName('playgroundgallery\controller\admin\galleryadmin');
        $this->assertControllerClass('GalleryAdminController');
        $this->assertActionName('createtag');
        $this->assertMatchedRouteName('admin/playgroundgallery/tag/create');
    }
    
    public function testDeleteActionCanBeAccessed()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $service = $this->getMockBuilder('PlaygroundGallery\Service\Tag')
            ->setMethods(array('findById', 'remove'))
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService('playgroundgallery_tag_service', $service);

        $id = 1;
        $tag = new TagEntity();
        $tag->setId($id)
            ->setName("Ceci est un nom");

        $service->expects($this->any())
            ->method('findById')
            ->will($this->returnValue($tag));
        $service->expects($this->any())
            ->method('remove')
            ->will($this->returnValue(null));

        $this->getRequest()
            ->setMethod('GET')
            ->setPost(new Parameters(array(
                'tagId' => $tag->getId()
        )));
        $this->dispatch('/admin/gallery/tag/remove');
        $this->assertModuleName('playgroundgallery');
        $this->assertControllerName('playgroundgallery\controller\admin\galleryadmin');
        $this->assertControllerClass('GalleryAdminController');
        $this->assertActionName('removetag');
        $this->assertMatchedRouteName('admin/playgroundgallery/tag/remove');

        $this->assertRedirectTo('/admin/gallery');
    }
    
    public function testEditActionCanBeAccessed()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $form = $this->getMockBuilder('PlaygroundGallery\Form\Tag')
            ->setMethods(array('bind', 'prepare', 'get'))
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService('playgroundgallery_tag_form', $form);

        $service = $this->getMockBuilder('PlaygroundGallery\Service\Tag')
            ->setMethods(array('findById'))
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService('playgroundgallery_tag_service', $service);

        $id = 1;
        $tag = new TagEntity();
        $tag->setId($id);

        $service->expects($this->any())
            ->method('findById')
            ->will($this->returnValue($tag));

        $this->dispatch('/admin/gallery/tag/edit');
        $this->assertModuleName('playgroundgallery');
        $this->assertControllerName('playgroundgallery\controller\admin\galleryadmin');
        $this->assertControllerClass('GalleryAdminController');
        $this->assertActionName('edittag');
        $this->assertMatchedRouteName('admin/playgroundgallery/tag/edit');
    }
    
}
