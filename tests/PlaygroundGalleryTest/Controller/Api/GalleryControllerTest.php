<?php

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Stdlib\Parameters;
use PlaygroundGallery\Entity\Media as MediaEntity;

class GalleryControllerTest extends AbstractHttpControllerTestCase
{
    
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../TestConfig.php'
        );
        parent::setUp();
    }
    
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/api/gallery/country/fr/offset/0/limit/20/tag/8/type/video');
        $this->assertResponseStatusCode(200);
        
        $this->assertModuleName('playgroundgallery');
        $this->assertControllerName('playgroundgallery\controller\api\gallery');
        $this->assertControllerClass('GalleryController');
        $this->assertActionName('list');
        $this->assertMatchedRouteName('api/gallery');
        
        $this->dispatch('/api/gallery/country/fr');
        $this->assertResponseStatusCode(200);
        
        $this->assertModuleName('playgroundgallery');
        $this->assertControllerName('playgroundgallery\controller\api\gallery');
        $this->assertControllerClass('GalleryController');
        $this->assertActionName('list');
        $this->assertMatchedRouteName('api/gallery');
    }
    
}
