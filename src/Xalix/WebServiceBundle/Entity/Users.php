<?php

namespace Xalix\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Users
 *
 * @ORM\Table(name="xl_users")
 * @ORM\Entity
 * @UniqueEntity(fields={"username"})
 * @ORM\Entity(repositoryClass="Xalix\WebServiceBundle\Entity\Repository\UsersRepository")
 */
class Users implements \JsonSerializable {

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
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity="WebService", mappedBy="user")
     * */
    private $webservice;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=32)
     */
    private $salt;

    /**
     * @var int
     *
     * @ORM\Column(name="cantidad_consultas", type="integer")
     */
    private $cantidad_consultas;

    /**
     * @ORM\OneToMany(targetEntity="Xalix\WebServiceBundle\Entity\AccessOrigin", mappedBy="user", cascade={"remove"})
     */
    private $accesos;

    public function __construct() {
        $this->webservice = new ArrayCollection();
        $this->accesos = new ArrayCollection();
        $this->cantidad_consultas = 0;
        $this->salt =  md5(uniqid(null,true));
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
     * Set username
     *
     * @param string $username
     * @return Users
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword() {
        return $this->password;
    }

    public function __toString() {
        return $this->getUsername();
    }

     /**
     * Add webservice
     *
     * @param \Xalix\WebServiceBundle\Entity\WebService $webservice
     * @return Users
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
        return array(
            'id'=>$this->id,
            'username'=>$this->username,
            'cantidad_consultas'=>$this->cantidad_consultas
        );
    }

    /**
     * @return int
     */
    public function getCantidadConsultas()
    {
        return $this->cantidad_consultas;
    }

    /**
     * @param int $cantidad_consultas
     */
    public function setCantidadConsultas($cantidad_consultas)
    {
        $this->cantidad_consultas = $cantidad_consultas;
    }



    /**
     * Add acceso
     *
     * @param \Xalix\WebServiceBundle\Entity\AccessOrigin $acceso
     *
     * @return Users
     */
    public function addAcceso(\Xalix\WebServiceBundle\Entity\AccessOrigin $acceso)
    {
        $this->accesos[] = $acceso;

        return $this;
    }

    /**
     * Remove acceso
     *
     * @param \Xalix\WebServiceBundle\Entity\AccessOrigin $acceso
     */
    public function removeAcceso(\Xalix\WebServiceBundle\Entity\AccessOrigin $acceso)
    {
        $this->accesos->removeElement($acceso);
    }

    /**
     * Get accesos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccesos()
    {
        return $this->accesos;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return Users
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }
}
