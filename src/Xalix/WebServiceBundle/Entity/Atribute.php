<?php

namespace Xalix\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Xalix\WebServiceBundle\Util\Util;

/**
 * Atribute
 *
 * @ORM\Table(name="xl_Atribute")
 * @ORM\Entity
 */
class Atribute
{
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
     * @ORM\Column(name="name", type="string", length=255)
     * Assert\NotBlank()
     * @Assert\Regex(pattern="/^[a-z A-Z]+/")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Xalix\WebServiceBundle\Entity\Type")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Xalix\WebServiceBundle\Entity\Type", inversedBy="atribute")
     */
    private $complexType;


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
     * Set name
     *
     * @param string $name
     * @return Atribute
     */
    public function setName($name)
    {
        $this->name = Util::getSlug($name);
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Atribute
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
     * Set complexType
     *
     * @param string $complexType
     * @return Atribute
     */
    public function setComplexType($complexType)
    {
        $this->complexType = $complexType;
    
        return $this;
    }

    /**
     * Get complexType
     *
     * @return string 
     */
    public function getComplexType()
    {
        return $this->complexType;
    }
    
    
    public function __toString() {
        return $this->getName();
    }


}
