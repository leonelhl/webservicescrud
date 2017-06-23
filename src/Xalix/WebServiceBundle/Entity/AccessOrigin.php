<?php

namespace Xalix\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccessOrigin
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Xalix\WebServiceBundle\Entity\Repository\AccessOriginRepository")
 */
class AccessOrigin
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
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255)
     */
    private $domain;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="accessDate", type="datetime")
     */
    private $accessDate;

    /**
     * @var string
     *
     * @ORM\Column(name="serviceConsumed", type="string", length=255)
     */
    private $serviceConsumed;

    /**
     * @ORM\ManyToOne(targetEntity="Xalix\WebServiceBundle\Entity\Users", inversedBy="accesos")
     */
    private $user;

    /**
     * AccessOrigin constructor.
     * @param string $ip
     * @param string $domain
     * @param string $serviceConsumed
     * @param $user
     */
    public function __construct($ip, $domain, $serviceConsumed, $user)
    {
        $this->ip = $ip;
        $this->domain = $domain;
        $this->serviceConsumed = $serviceConsumed;
        $this->user = $user;
        $this->accessDate = new \DateTime('now');
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
     * Set ip
     *
     * @param string $ip
     *
     * @return AccessOrigin
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set domain
     *
     * @param string $domain
     *
     * @return AccessOrigin
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set accessDate
     *
     * @param \DateTime $accessDate
     *
     * @return AccessOrigin
     */
    public function setAccessDate($accessDate)
    {
        $this->accessDate = $accessDate;

        return $this;
    }

    /**
     * Get accessDate
     *
     * @return \DateTime
     */
    public function getAccessDate()
    {
        return $this->accessDate;
    }

    /**
     * Set serviceConsumed
     *
     * @param string $serviceConsumed
     *
     * @return AccessOrigin
     */
    public function setServiceConsumed($serviceConsumed)
    {
        $this->serviceConsumed = $serviceConsumed;

        return $this;
    }

    /**
     * Get serviceConsumed
     *
     * @return string
     */
    public function getServiceConsumed()
    {
        return $this->serviceConsumed;
    }

    /**
     * Set user
     *
     * @param \Xalix\WebServiceBundle\Entity\Users $user
     *
     * @return AccessOrigin
     */
    public function setUser(\Xalix\WebServiceBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Xalix\WebServiceBundle\Entity\Users
     */
    public function getUser()
    {
        return $this->user;
    }


}
