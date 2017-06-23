<?php

namespace Xalix\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Method
 *
 * @ORM\Table(name="xl_Method")
 * @ORM\Entity
 */
class Method {

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
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Xalix\WebServiceBundle\Entity\Protocol")
     */
    private $protocol;

    
    /**
     * @ORM\ManyToMany(targetEntity="WsFunction", mappedBy="method")
     **/
    private $wsfunction;
    
    public function __construct() {
        $this->wsfunction = new ArrayCollection();
    }
    
    public function __toString() {

        return $this->getName();
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
     * Set name
     *
     * @param string $name
     * @return Method
     */
    public function setName($name)
    {
        $this->name = $name;
    
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
     * Set protocol
     *
     * @param \Xalix\WebServiceBundle\Entity\Protocol $protocol
     * @return Method
     */
    public function setProtocol(\Xalix\WebServiceBundle\Entity\Protocol $protocol = null)
    {
        $this->protocol = $protocol;
    
        return $this;
    }

    /**
     * Get protocol
     *
     * @return \Xalix\WebServiceBundle\Entity\Protocol 
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Add wsfunction
     *
     * @param \Xalix\WebServiceBundle\Entity\WsFunction $wsfunction
     * @return Method
     */
    public function addWsfunction(\Xalix\WebServiceBundle\Entity\WsFunction $wsfunction)
    {
        $this->wsfunction[] = $wsfunction;
    
        return $this;
    }

    /**
     * Remove wsfunction
     *
     * @param \Xalix\WebServiceBundle\Entity\WsFunction $wsfunction
     */
    public function removeWsfunction(\Xalix\WebServiceBundle\Entity\WsFunction $wsfunction)
    {
        $this->wsfunction->removeElement($wsfunction);
    }

    /**
     * Get wsfunction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWsfunction()
    {
        return $this->wsfunction;
    }
}
