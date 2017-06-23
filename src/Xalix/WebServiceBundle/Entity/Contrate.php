<?php

namespace Xalix\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contrate
 *
 * @ORM\Table(name="xl_Contrate")
 * @ORM\Entity
 */
class Contrate {

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
     * @ORM\Column(name="file", type="string", length=255)
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="Xalix\WebServiceBundle\Entity\Protocol")
     */
    private $protocol;

    /**
     * @ORM\ManyToOne(targetEntity="Xalix\WebServiceBundle\Entity\WebService", inversedBy="contrate")
     */
    private $webservice;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return Contrate
     */
    public function setFile($file) {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Set protocol
     *
     * @param \Xalix\WebServiceBundle\Entity\Protocol $protocol
     * @return Contrate
     */
    public function setProtocol(\Xalix\WebServiceBundle\Entity\Protocol $protocol = null) {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get protocol
     *
     * @return \Xalix\WebServiceBundle\Entity\Protocol 
     */
    public function getProtocol() {
        return $this->protocol;
    }

    /**
     * Set webservice
     *
     * @param \Xalix\WebServiceBundle\Entity\WebService $webservice
     * @return Contrate
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

    public function __toString() {
        /*
         * Su objetivo es leer el archivo file y serializarlo para 
         * poder mostrarlo en pantalla.
         */
        try {
            if($this->getProtocol()->getName() == 'SOAP'){
               return file_get_contents(__DIR__ . '/../WebServices/Contrates/soap/' . $this->getFile()); 
            }
            if($this->getProtocol()->getName() == 'REST'){
               return file_get_contents(__DIR__ . '/../WebServices/Contrates/rest/' . $this->getFile()); 
            }
        } catch (\Exception $e) {
            return $this->getFile();
        }
    }

}
