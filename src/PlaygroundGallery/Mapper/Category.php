<?php

namespace PlaygroundGallery\Mapper;

use Doctrine\ORM\EntityManager;
use ZfcBase\Mapper\AbstractDbMapper;

use PlaygroundGallery\Options\ModuleOptions;

class Category
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $er;

    /**
     * @var \PlaygroundGallery\Options\ModuleOptions
     */
    protected $options;


    /**
    * __construct
    * @param Doctrine\ORM\EntityManager $em
    * @param PlaygroundGallery\Options\ModuleOptions $options
    *
    */
    public function __construct(EntityManager $em, ModuleOptions $options)
    {
        $this->em      = $em;
        $this->options = $options;
    }

    /**
    * findById : recupere l'entite en fonction de son id
    * @param int $id id de la company
    *
    * @return PlaygroundGallery\Entity\Category $category
    */
    public function findById($id)
    {
        return $this->getEntityRepository()->find($id);
    }

    /**
    * insert : insert en base une entité category
    * @param PlaygroundGallery\Entity\Category $category category
    *
    * @return PlaygroundGallery\Entity\Category $category
    */
    public function insert($entity)
    {
        return $this->persist($entity);
    }

    /**
    * insert : met a jour en base une entité category
    * @param PlaygroundGallery\Entity\Category $category category
    *
    * @return PlaygroundGallery\Entity\Category $category
    */
    public function update($entity)
    {
        return $this->persist($entity);
    }

    /**
    * findBy : recupere des entites en fonction de filtre
    * @param array $filter tableau de filtre
    *
    * @return collection $comments collection de Synthesio\Entity\Comment
    */
    public function findBy($filter, $order = null, $limit = null, $offset = null)
    {
        return $this->getEntityRepository()->findBy($filter, $order, $limit, $offset);
    }

    /**
    * insert : met a jour en base une entité company et persiste en base
    * @param PlaygroundGallery\Entity\Category $entity category
    *
    * @return PlaygroundGallery\Entity\Category $category
    */
    protected function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    /**
    * findAll : recupere toutes les entites
    *
    * @return collection $category collection de PlaygroundGallery\Entity\Category
    */
    public function findAll()
    {
        return $this->getEntityRepository()->findAll();
    }

     /**
    * remove : supprimer une entite category
    * @param PlaygroundGallery\Entity\Category $category Category
    *
    */
    public function remove($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
    * getEntityRepository : recupere l'entite category
    *
    * @return PlaygroundGallery\Entity\Category $category
    */
    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('PlaygroundGallery\Entity\Category');
        }

        return $this->er;
    }
}