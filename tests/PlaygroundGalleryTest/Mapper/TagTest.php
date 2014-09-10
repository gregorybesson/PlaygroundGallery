<?php
namespace PlaygroundGalleryTest\Mapper;

use PlaygroundGalleryTest\Bootstrap;
use \PlaygroundGallery\Entity\Tag as TagEntity;

class TagTest extends \PHPUnit_Framework_TestCase
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
    protected $tagData;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        $this->tagData = array(
            'name' => 'Tag Name @*$',
        );

        parent::setUp();
    }

    public function testCanInsertNewRecord()
    {
        $tag = new TagEntity();
        $tag->populate($this->tagData);
        // save data
        $this->em->persist($tag);
        $this->em->flush();

        $this->assertEquals($this->tagData['name'], $tag->getName());

        return $tag->getId();
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanUpdateInsertedRecord($id)
    {
        $data = array(
            'id' => $id
        );
        $tag = $this->em->getRepository('PlaygroundGallery\Entity\Tag')->find($id);
        $this->assertInstanceOf('PlaygroundGallery\Entity\Tag', $tag);
        $this->assertEquals($this->tagData['name'], $tag->getName());

        $tag->populate($data);
        $this->em->flush();

        $this->assertEquals($this->tagData['name'], $tag->getName());
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanRemoveInsertedRecord($id)
    {
        $tag = $this->em->getRepository('PlaygroundGallery\Entity\Tag')->find($id);
        $this->assertInstanceOf('PlaygroundGallery\Entity\Tag', $tag);

        $this->em->remove($tag);
        $this->em->flush();

        $tag = $this->em->getRepository('PlaygroundGallery\Entity\Tag')->find($id);
        $this->assertEquals(false, $tag);
    }

    public function tearDown()
    {
        $dbh = $this->em->getConnection();

        unset($this->sm);
        unset($this->em);
        parent::tearDown();
    }
}