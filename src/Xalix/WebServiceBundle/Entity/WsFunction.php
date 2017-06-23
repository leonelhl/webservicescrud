<?php

namespace Xalix\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Xalix\WebServiceBundle\Util\Util;

/**
 * WsFunction
 *
 * @ORM\Table(name="xl_WsFunction")
 * @ORM\Entity
 */
class WsFunction {

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
     * @ORM\Column(name="name", type="string", length=30)
     * @Assert\NotBlank()
     * @Assert\Length(min = 3, max = 30)
     * @Assert\Regex(pattern="/^[a-z A-Z]+/")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Xalix\WebServiceBundle\Entity\Type")
     * @Assert\NotBlank()
     */
    private $return;

    /**
     * @ORM\ManyToOne(targetEntity="Xalix\WebServiceBundle\Entity\WebService", inversedBy="wsfunction")
     */
    private $webservice;

    /**
     * @ORM\OneToMany(targetEntity="Xalix\WebServiceBundle\Entity\Param", mappedBy="wsfunction", cascade={"persist","remove"})
     * @Assert\NotBlank()
     */
    private $param;

    /**
     * @ORM\ManyToOne(targetEntity="Xalix\WebServiceBundle\Entity\Method")
     */
    private $method;

    public function __construct() {
        $this->param = new ArrayCollection();
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
     * @return WsFunction
     */
    public function setName($name) {
        $this->name = Util::getSlug($name);
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
     * @return WsFunction
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
     * Set return
     *
     * @param \Xalix\WebServiceBundle\Entity\Type $return
     * @return WsFunction
     */
    public function setReturn(\Xalix\WebServiceBundle\Entity\Type $return = null) {
        $this->return = $return;

        return $this;
    }

    /**
     * Get return
     *
     * @return \Xalix\WebServiceBundle\Entity\Type 
     */
    public function getReturn() {
        return $this->return;
    }

    /**
     * Set webservice
     *
     * @param \Xalix\WebServiceBundle\Entity\WebService $webservice
     * @return WsFunction
     */
    public function setWebservice(\Xalix\WebServiceBundle\Entity\WebService $webservice = null) {
        $this->webservice = $webservice;

        return $this;
    }

    /**
     * Get webservice
     *
     * @return \Xalix\WebServiceBundle\Entity\WebService 
     */
    public function getWebservice() {
        return $this->webservice;
    }

    /**
     * Add param
     *
     * @param \Xalix\WebServiceBundle\Entity\Param $param
     * @return WsFunction
     */
    public function addParam(\Xalix\WebServiceBundle\Entity\Param $param) {
        $param->setWsfunction($this);
        $this->param[] = $param;

        return $this;
    }

    /**
     * Remove param
     *
     * @param \Xalix\WebServiceBundle\Entity\Param $param
     */
    public function removeParam(\Xalix\WebServiceBundle\Entity\Param $param) {
        $this->param->removeElement($param);
    }

    /**
     * Get param
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParam() {
        return $this->param;
    }

    /**
     * Add method
     *
     * @param \Xalix\WebServiceBundle\Entity\Method $method
     * @return WsFunction
     */
    public function setMethod(\Xalix\WebServiceBundle\Entity\Method $method) {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return \Xalix\WebServiceBundle\Entity\Method
     */
    public function getMethod() {
        return $this->method;
    }

}
