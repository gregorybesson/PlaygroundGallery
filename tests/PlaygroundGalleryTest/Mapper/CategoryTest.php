<?php
namespace PlaygroundGalleryTest\Mapper;

use PlaygroundGalleryTest\Bootstrap;
use \PlaygroundGallery\Entity\Category as CategoryEntity;

class CategoryTest extends \PHPUnit_Framework_TestCase
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
     * Category sample
     * @var Array
     */
    protected $categoryData;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        $this->categoryData = array(
            'name' => 'CeciEstUnTitre',
        );

        parent::setUp();
    }

    public function testCanInsertNewRecord()
    {
        $category = new CategoryEntity();
        $category->populate($this->categoryData);

        // save data
        $this->em->persist($category);
        $this->em->flush();

        $this->assertEquals($this->categoryData['name'], $category->getName());

        return $category->getId();
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanUpdateInsertedRecord($id)
    {
        $data = array(
            'id' => $id
        );
        $category = $this->em->getRepository('PlaygroundGallery\Entity\Category')->find($id);
        $this->assertInstanceOf('PlaygroundGallery\Entity\Category', $category);
        $this->assertEquals($this->categoryData['name'], $category->getName());

        $category->populate($data);
        $this->em->flush();

        $this->assertEquals($this->categoryData['name'], $category->getName());
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanRemoveInsertedRecord($id)
    {
        $category = $this->em->getRepository('PlaygroundGallery\Entity\Category')->find($id);
        $this->assertInstanceOf('PlaygroundGallery\Entity\Category', $category);

        $this->em->remove($category);
        $this->em->flush();

        $category = $this->em->getRepository('PlaygroundGallery\Entity\Category')->find($id);
        $this->assertEquals(false, $category);
    }

    public function tearDown()
    {
        $dbh = $this->em->getConnection();

        unset($this->sm);
        unset($this->em);
        parent::tearDown();
    }
}