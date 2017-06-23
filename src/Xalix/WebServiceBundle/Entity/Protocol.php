<?php

namespace Xalix\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Protocol
 *
 * @ORM\Table(name="xl_Protocol")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Xalix\WebServiceBundle\Entity\Repository\ProtocolRepository")
 */
class Protocol {

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
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="WebService", mappedBy="protocol")
     * */
    private $webservice;

    public function __construct() {
        $this->webservice = new ArrayCollection();
    }

    
    public function __toString() {

        return $this->getName();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Protocol
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {

        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Protocol
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {

        return $this->description;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Protocol
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive() {

        return $this->isActive;
    }


    /**
     * Add webservice
     *
     * @param \Xalix\WebServiceBundle\Entity\WebService $webservice
     * @return Protocol
     */
    public function addWebservice(\Xalix\WebServiceBundle\Entity\WebService $webservice)
    {
        $this->webservice[] = $webservice;
    
        return $this;
    }

    /**
     * Remove webservice
     *
     * @param \Xalix\WebServiceBundle\Entity\WebService $webservice
     */
    public function removeWebservice(\Xalix\WebServiceBundle\Entity\WebService $webservice)
    {
        $this->webservice->removeElement($webservice);
    }

    /**
     * Get webservice
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWebservice()
    {
        return $this->webservice;
    }
}
