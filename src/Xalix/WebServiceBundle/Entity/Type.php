<?php

namespace Xalix\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Type
 *
 * @ORM\Table(name="xl_Type")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Xalix\WebServiceBundle\Entity\Repository\TypeRepository")
 */
class Type {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[a-z A-Z]+/")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="oI", type="string", length=10, nullable=true)
     * @Assert\Choice(choices = {"all", "sequence", "choice"})
     */
    private $orderIndicator;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_complexType", type="boolean")
     */
    private $isComplexType;

    
     /**
     * @var boolean
     *
     * @ORM\Column(name="is_array", type="boolean")
     */
    private $isArray;
    
   /**
     * @ORM\OneToMany(targetEntity="Xalix\WebServiceBundle\Entity\Atribute", mappedBy="complexType", cascade={"persist", "remove"})
     */
    private $atribute;

    
    public function __construct() {
        $this->atribute = new ArrayCollection();
    }
 
 
    public function __toString() {
        return $this->getType();
    }



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Type
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }
    
    
    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get orderIndicator
     *
     * @return string 
     */
    public function getOrderIndicator()
    {
        return $this->orderIndicator;
    }

     /**
     * Set orderIndicator
     *
     * @param string $orderIndicator
     * @return orderIndicator
     */
    public function setOrderIndicator($orderIndicator)
    {
        $this->orderIndicator = $orderIndicator;
    
        return $this;
    }
    
    /**
     * Set isComplexType
     *
     * @param boolean $isComplexType
     * @return Type
     */
    public function setIsComplexType($isComplexType)
    {
        $this->isComplexType = $isComplexType;
    
        return $this;
    }

    /**
     * Get isComplexType
     *
     * @return boolean 
     */
    public function getIsComplexType()
    {
        return $this->isComplexType;
    }

    /**
     * Set isArray
     *
     * @param boolean $isArray
     * @return Type
     */
    public function setIsArray($isArray)
    {
        $this->isArray = $isArray;
    
        return $this;
    }

    /**
     * Get isArray
     *
     * @return boolean 
     */
    public function getIsArray()
    {
        return $this->isArray;
    }

    /**
     * Add atribute
     *
     * @param \Xalix\WebServiceBundle\Entity\Atribute $atribute
     * @return Type
     */
    public function addAtribute(\Xalix\WebServiceBundle\Entity\Atribute $atribute)
    {
        $atribute->setComplexType($this);
        $this->atribute[] = $atribute;
    
        return $this;
    }

    /**
     * Remove atribute
     *
     * @param \Xalix\WebServiceBundle\Entity\Atribute $atribute
     */
    public function removeAtribute(\Xalix\WebServiceBundle\Entity\Atribute $atribute)
    {
        $this->atribute->removeElement($atribute);
    }

    /**
     * Get atribute
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAtribute()
    {
        return $this->atribute;
    }
}
