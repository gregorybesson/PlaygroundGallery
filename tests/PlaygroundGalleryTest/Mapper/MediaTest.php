<?php
namespace PlaygroundGalleryTest\Mapper;

use PlaygroundGalleryTest\Bootstrap;
use \PlaygroundGallery\Entity\Media as MediaEntity;

class MediaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Service Manager
     * @var Zend\ServiceManager\ServiceManager
     */
    protected $sm;

   /**
     * Doctrine Entity Manager
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Company sample
     * @var Array
     */
    protected $mediaData;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        $this->mediaData = array(
            'name' => 'CeciEstUnTitre',
            'credit' => 'CeciEstUnCredit',
            'url' => 'http://lorempixel.com/400/600/sports/2/',
            'description' => 'CeciEstUneDescription',
            'poster'      =>  'http://lorempixel.com/400/600/sports/3/',
        );

        parent::setUp();
    }

    public function testCanInsertNewRecord()
    {
        $media = new MediaEntity();
        $media->populate($this->mediaData);
        // save data
        $this->em->persist($media);
        $this->em->flush();

        $this->assertEquals($this->mediaData['name'], $media->getName());
        $this->assertEquals($this->mediaData['credit'], $media->getCredit());
        $this->assertEquals($this->mediaData['url'], $media->getUrl());
        $this->assertEquals($this->mediaData['poster'], $media->getPoster());
        $this->assertEquals($this->mediaData['description'], $media->getDescription());

        return $media->getId();
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanUpdateInsertedRecord($id)
    {
        $data = array(
            'id' => $id
        );
        $media = $this->em->getRepository('PlaygroundGallery\Entity\Media')->find($id);
        $this->assertInstanceOf('PlaygroundGallery\Entity\Media', $media);
        $this->assertEquals($this->mediaData['name'], $media->getName());

        $media->populate($data);
        $this->em->flush();

        $this->assertEquals($this->mediaData['name'], $media->getName());
        $this->assertEquals($this->mediaData['credit'], $media->getCredit());
        $this->assertEquals($this->mediaData['url'], $media->getUrl());
        $this->assertEquals($this->mediaData['description'], $media->getDescription());
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanRemoveInsertedRecord($id)
    {
        $media = $this->em->getRepository('PlaygroundGallery\Entity\Media')->find($id);
        $this->assertInstanceOf('PlaygroundGallery\Entity\Media', $media);

        $this->em->remove($media);
        $this->em->flush();

        $media = $this->em->getRepository('PlaygroundGallery\Entity\Media')->find($id);
        $this->assertEquals(false, $media);
    }

    public function tearDown()
    {
        $dbh = $this->em->getConnection();

        unset($this->sm);
        unset($this->em);
        parent::tearDown();
    }
}