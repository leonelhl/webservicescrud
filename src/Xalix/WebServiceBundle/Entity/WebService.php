<?php

namespace Xalix\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Xalix\WebServiceBundle\Util\Util;

/**
 * WebService
 *
 * @ORM\Table(name="xl_WebService")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Xalix\WebServiceBundle\Entity\Repository\WebServiceRepository")
 * @DoctrineAssert\UniqueEntity("name")
 * @DoctrineAssert\UniqueEntity("slug")
 * @DoctrineAssert\UniqueEntity("uri")
 * @DoctrineAssert\UniqueEntity("class")
 */
class WebService {

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
     * @ORM\Column(name="name", type="string", length=30, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min = 4, max = 30)
     * @Assert\Regex(pattern="/^[a-z A-Z]+/")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=100, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min = 4)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="uri", type="string", length=255, unique=true)
     * 
     */
    private $uri;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_token", type="boolean")
     */
    private $isToken;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_user", type="boolean")
     */
    private $isUser;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * 
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Xalix\WebServiceBundle\Entity\WsFunction", mappedBy="webservice", cascade={"persist","remove"})
     * @Assert\Valid()
     */
    private $wsfunction;

    /**
     * @ORM\OneToMany(targetEntity="Xalix\WebServiceBundle\Entity\Contrate", mappedBy="webservice", cascade={"persist","remove"})
     */
    private $contrate;

    /**
     * @ORM\ManyToMany(targetEntity="Protocol", inversedBy="webservice")
     * @ORM\JoinTable(name="xl_webservice_protocol")
     * */
    private $protocol;

    /**
     * @ORM\ManyToMany(targetEntity="Token", inversedBy="webservice")
     * @ORM\JoinTable(name="xl_webservice_token")
     * */
    private $token;

    /**
     * @ORM\ManyToMany(targetEntity="Users", inversedBy="webservice")
     * @ORM\JoinTable(name="xl_webservice_users")
     * */
    private $user;

    public function __construct() {
        $this->wsfunction = new ArrayCollection();
        $this->contrate = new ArrayCollection();
        // $this->relationall = new ArrayCollection();
        $this->protocol = new ArrayCollection();
        $this->token = new ArrayCollection();
        $this->user = new ArrayCollection();
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
     * @return WebService
     */
    public function setName($name) {
        $this->name = $name;
        $this->slug = Util::getSlug($name);
        /*
         * Aquí tengo que buscar una función que me de el dominio
         * donde se está ejecutando el servidor
         */

        //esto es en el servidor para tomar el contexto y ponerselo al server
        //$this->uri = "http://localhost/nusoap/".$this->slug.".php";
        $this->class = $this->slug . ".php";
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
     * Set slug
     *
     * @param string $slug
     * @return WebService
     */
    public function setSlug($slug) {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * Set uri
     *
     * @param string $uri
     * @return WebService
     */
    public function setUri($uri) {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri
     *
     * @return string 
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return WebService
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
     * Set class
     *
     * @param string $class
     * @return WebService
     */
    public function setClass($class) {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return WebService
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
     * Add wsfunction
     *
     * @param \Xalix\WebServiceBundle\Entity\WsFunction $wsfunction
     * @return WebService
     */
    public function addWsfunction(\Xalix\WebServiceBundle\Entity\WsFunction $wsfunction) {
        $wsfunction->setWebservice($this);
        $this->wsfunction[] = $wsfunction;
        return $this;
    }

    /**
     * Remove wsfunction
     *
     * @param \Xalix\WebServiceBundle\Entity\WsFunction $wsfunction
     */
    public function removeWsfunction(\Xalix\WebServiceBundle\Entity\WsFunction $wsfunction) {
        $this->wsfunction->removeElement($wsfunction);
    }

    /**
     * Get wsfunction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWsfunction() {
        return $this->wsfunction;
    }

    /**
     * Add contrate
     *
     * @param \Xalix\WebServiceBundle\Entity\Contrate $contrate
     * @return WebService
     */
    public function addContrate(\Xalix\WebServiceBundle\Entity\Contrate $contrate) {
        $contrate->setWebservice($this);
        $this->contrate[] = $contrate;

        return $this;
    }

    /**
     * Remove contrate
     *
     * @param \Xalix\WebServiceBundle\Entity\Contrate $contrate
     */
    public function removeContrate(\Xalix\WebServiceBundle\Entity\Contrate $contrate) {
        $this->contrate->removeElement($contrate);
    }

    /**
     * Get contrate
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContrate() {
        return $this->contrate;
    }

    /**
     * Add protocol
     *
     * @param \Xalix\WebServiceBundle\Entity\Protocol $protocol
     * @return WebService
     */
    public function addProtocol(\Xalix\WebServiceBundle\Entity\Protocol $protocol) {

        $this->protocol[] = $protocol;

        return $this;
    }

    /**
     * Remove protocol
     *
     * @param \Xalix\WebServiceBundle\Entity\Protocol $protocol
     */
    public function removeProtocol(\Xalix\WebServiceBundle\Entity\Protocol $protocol) {
        $this->protocol->removeElement($protocol);
    }

    /**
     * Get protocol
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProtocol() {
        return $this->protocol;
    }


    /**
     * Add token
     *
     * @param \Xalix\WebServiceBundle\Entity\Token $token
     * @return WebService
     */
    public function addToken(\Xalix\WebServiceBundle\Entity\Token $token)
    {
        $this->token[] = $token;
    
        return $this;
    }

    /**
     * Remove token
     *
     * @param \Xalix\WebServiceBundle\Entity\Token $token
     */
    public function removeToken(\Xalix\WebServiceBundle\Entity\Token $token)
    {
        $this->token->removeElement($token);
    }

    /**
     * Get token
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Add user
     *
     * @param \Xalix\WebServiceBundle\Entity\Users $user
     * @return WebService
     */
    public function addUser(\Xalix\WebServiceBundle\Entity\Users $user)
    {
        $this->user[] = $user;
    
        return $this;
    }

    /**
     * Remove user
     *
     * @param \Xalix\WebServiceBundle\Entity\Users $user
     */
    public function removeUser(\Xalix\WebServiceBundle\Entity\Users $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set isToken
     *
     * @param boolean $isToken
     * @return WebService
     */
    public function setIsToken($isToken)
    {
        $this->isToken = $isToken;
    
        return $this;
    }

    /**
     * Get isToken
     *
     * @return boolean 
     */
    public function getIsToken()
    {
        return $this->isToken;
    }

    /**
     * Set isUser
     *
     * @param boolean $isUser
     * @return WebService
     */
    public function setIsUser($isUser)
    {
        $this->isUser = $isUser;
    
        return $this;
    }

    /**
     * Get isUser
     *
     * @return boolean 
     */
    public function getIsUser()
    {
        return $this->isUser;
    }
}
