<?php

namespace Xalix\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Xalix\WebServiceBundle\Util\Util;

/**
 * Param
 *
 * @ORM\Table(name="xl_Param")
 * @ORM\Entity
 */
class Param {

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
     * @ORM\Column(name="name", type="string", length=20)
     * @Assert\NotBlank()
     * @Assert\Length(max = 20)
     * @Assert\Regex(pattern="/^[a-z A-Z]+/")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Xalix\WebServiceBundle\Entity\WsFunction", inversedBy="param")
     */
    private $wsfunction;

    /**
     * @ORM\ManyToOne(targetEntity="Xalix\WebServiceBundle\Entity\Type")
     * @Assert\NotBlank()
     */
    private $type;

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
     * @return Param
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
     * Set wsfunction
     *
     * @param \Xalix\WebServiceBundle\Entity\WsFunction $wsfunction
     * @return Param
     */
    public function setWsfunction(\Xalix\WebServiceBundle\Entity\WsFunction $wsfunction = null)
    {
        $this->wsfunction = $wsfunction;
    
        return $this;
    }

    /**
     * Get wsfunction
     *
     * @return \Xalix\WebServiceBundle\Entity\WsFunction 
     */
    public function getWsfunction()
    {
        return $this->wsfunction;
    }

    /**
     * Set type
     *
     * @param \Xalix\WebServiceBundle\Entity\Type $type
     * @return Param
     */
    public function setType(\Xalix\WebServiceBundle\Entity\Type $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \Xalix\WebServiceBundle\Entity\Type 
     */
    public function getType()
    {
        return $this->type;
    }
       
    public function __toString() {

        return $this->getName();
    }

}
