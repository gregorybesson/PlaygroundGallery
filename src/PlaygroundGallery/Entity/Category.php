<?php

namespace PlaygroundGallery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="gallery_category")
 */
class Category implements InputFilterAwareInterface
{

    protected $inputFilter;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * name
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="PlaygroundGallery\Entity\Category", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="PlaygroundGallery\Entity\Category", inversedBy="children")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="PlaygroundGallery\Entity\Media", mappedBy="category")
     */
    protected $medias;

     /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="PlaygroundCore\Entity\Locale")
     * @ORM\JoinTable(name="gallery_category_locale",
     *      joinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="locale_id", referencedColumnName="id")}
     * )
     */
    protected $locales;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;


    public function __construct()
    {
        $this->locales = new ArrayCollection();
    }

     /**
     * Get locales.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Add a locale to the user.
     *
     * @param Locale $locale
     *
     * @return void
     */
    public function addLocale($locale)
    {
        $this->locales[] = $locale;
    }

    public function removeLocale(){
        $this->locales = new ArrayCollection();

        return $this;
    }


    /**
     * @param string $id
     * @return Category
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /** @PrePersist */
    public function createChrono()
    {
        $this->created_at = new \DateTime("now");
        $this->updated_at = new \DateTime("now");
    }

    /** @PreUpdate */
    public function updateChrono()
    {
        $this->updated_at = new \DateTime("now");
    }

    /**
     * @param string $Category
     * @return Category
     */
    public function setParent(Category $category)
    {
        $this->parent = $category;

        return $this;
    }

    /**
     * @return string $category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return PlaygroundGallery\Entity\Category $locales
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * @param PlaygroundGallery\Entity\Category $categories
     * @return Locale
     */
    public function setChildren($categories)
    {
        $this->children = $categories;
    
        return $this;
    }
    
    /**
     * @param PlaygroundGallery\Entity\Category $category
     * @return Locale
     */
    public function addChildren($category)
    {
        $this->children[] = $category;
    
        return $this;
    }
    
    /**
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $medias
     * @return Category
     */
    public function setMedias($medias)
    {
        $this->medias = $medias;

        return $this;
    }

    public function addMedia($media)
    {
        $this->medias[] = $media;

        return $this;
    }

    /**
     * @return string $media
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @return the unknown_type
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param unknown_type $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return the unknown_type
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param unknown_type $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getArrayCopy ()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        if (isset($data['parent']) && $data['parent'] != null) {
            $this->parent = $data['parent'];
        }
        if (isset($data['name']) && $data['name'] != null) {
            $this->name = $data['name'];
        }
    }



    /**
    * setInputFilter
    * @param InputFilterInterface $inputFilter
    */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
    * getInputFilter
    *
    * @return  InputFilter $inputFilter
    */
    public function getInputFilter()
    {
         if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}