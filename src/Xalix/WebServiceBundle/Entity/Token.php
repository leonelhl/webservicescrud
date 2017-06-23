<?php

namespace Xalix\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Token
 *
 * @ORM\Table(name="xl_token")
 * @ORM\Entity
 */
class Token implements \JsonSerializable {

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
     * @ORM\Column(name="token", type="string", length=50)
     */
    private $token;

    /**
     * @ORM\ManyToMany(targetEntity="WebService", mappedBy="token")
     * */
    private $webservice;

    public function __construct() {
        $this->webservice = new ArrayCollection();
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
     * Set token
     *
     * @param string $token
     * @return Token
     */
    public function setToken($token) {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken() {
        return $this->token;
    }

    public function __toString() {
        return $this->getToken();
    }

    /**
     * Add webservice
     *
     * @param \Xalix\WebServiceBundle\Entity\WebService $webservice
     * @return Token
     */
    public function addWebservice(\Xalix\WebServiceBundle\Entity\WebService $webservice) {
        $this->webservice[] = $webservice;

        return $this;
    }

    /**
     * Remove webservice
     *
     * @param \Xalix\WebServiceBundle\Entity\WebService $webservice
     */
    public function removeWebservice(\Xalix\WebServiceBundle\Entity\WebService $webservice) {
        $this->webservice->removeElement($webservice);
    }

    /**
     * Get webservice
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWebservice() {
        return $this->webservice;
    }


    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
        return array('id'=>$this->id, 'token'=>$this->token);
    }
}
