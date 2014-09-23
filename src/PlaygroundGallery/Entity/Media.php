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


/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="gallery_media")
 */
class Media implements InputFilterAwareInterface
{

    protected $inputFilter;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="PlaygroundGallery\Entity\Category", inversedBy="medias")
     */
    protected $category;

    /**
     * name
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * credit
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $credit;

    /**
     * description
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $description;
    
    /**
     * url
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $url;

    /**
     * poster
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $poster;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="PlaygroundGallery\Entity\Tag")
     * @ORM\JoinTable(name="gallery_media_tag",
     *      joinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */
    protected $tags;

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
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @param string $id
     * @return Media
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
     * @param string $category
     * @return Media
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string $category
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * @param string $name
     * @return Media
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
     * @param string $credit
     * @return Media
     */
    public function setCredit($credit)
    {
    	$this->credit = (string) $credit;
    
    	return $this;
    }
    
    /**
     * @return string $credit
     */
    public function getCredit()
    {
    	return $this->credit;
    }

    /**
     * @param string $description
     * @return Media
     */
    public function setDescription($description)
    {
    	$this->description = (string) $description;
    
    	return $this;
    }
    
    /**
     * @return string $description
     */
    public function getDescription()
    {
    	return $this->description;
    }
    
    /**
     * @param string $url
     * @return Media
     */
    public function setUrl($url)
    {
    	$this->url = (string) $url;
    
    	return $this;
    }
    
    /**
     * @return string $url
     */
    public function getUrl()
    {
    	return $this->url;
    }

    /**
     * @param string $poster
     * @return Media
     */
    public function setPoster($poster)
    {
        $this->poster = (string) $poster;
    
        return $this;
    }
    
    /**
     * @return string $poster
     */
    public function getPoster()
    {
        return $this->poster;
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
    
    
    public function setTags($tags)
    {
        $this->tags = $tags;
    }
    
   
    public function getTags()
    {
        return $this->tags;
    }
    
    public function addTag($tag)
    {
        $this->tags[] = $tag;
    }
    
    public function removeTag(){
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    
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
    	if (isset($data['id']) && $data['id'] != null) {
            $this->id = $data['id'];
        }
        if (isset($data['name']) && $data['name'] != null) {
        	$this->name = $data['name'];
        }
        if (isset($data['credit']) && $data['credit'] != null) {
        	$this->credit = $data['credit'];
        }
        if (isset($data['url']) && $data['url'] != null) {
        	$this->url = $data['url'];
        }
        if (isset($data['poster']) && $data['poster'] != null) {
            $this->poster = $data['poster'];
        }
        if (isset($data['description']) && $data['description'] != null) {
            $this->description = $data['description'];
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