<?php
namespace PlaygroundGallery\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;


/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="gallery_tag")
 */
class Tag implements InputFilterAwareInterface
{
    
    protected $inputFilter;
    
     /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", unique=true,  length=255)
     */
    protected $name;
    
    /**
     * @ORM\ManyToMany(targetEntity="Media", mappedBy="tags")
     */
    protected $medias;
    
    /**
     * @ORM\OneToMany(targetEntity="PlaygroundGallery\Entity\Tag", mappedBy="parent", cascade={"remove"})
     */
    protected $children;
    
    /**
     * @ORM\ManyToOne(targetEntity="PlaygroundGallery\Entity\Tag", inversedBy="children")
     */
    protected $parent;
    
    public function __construct()
    {
        $this->medias = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->id = $name;
        return $this;
    }
    
    public function addMedia(Media $media)
    {
        $this->medias[] = $media;
        return $this;
    }
    
    public function getChildren()
    {
        return $this->children;
    }
    
    public function getParent()
    {
        return $this->parent;
    }
    
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }
    
    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
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
            $this->id    = $data['id'];
        }
    
        if (isset($data['name']) && $data['name'] != null) {
            $this->name    = $data['name'];
        }
        return $this;
    }
    
    /**
     * @return InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
    
            $inputFilter->add($factory->createInput(array(
                'name'       => 'id',
                'required'   => false,
                'filters' => array(
                    array('name'    => 'Int'),
                ),
            )));
    
            $inputFilter->add($factory->createInput(array(
                'name'     => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 255,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'parent',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
    
            $this->inputFilter = $inputFilter;
        }
    
        return $this->inputFilter;
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used" .$inputFilter);
    }

}