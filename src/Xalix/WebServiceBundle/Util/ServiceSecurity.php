<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Xalix\WebServiceBundle\Util;

/**
 * Description of newPHPClass
 *
 * @author Leox
 */
class ServiceSecurity {

    private $request;
    private $doctrine;
    private $encoder;
    private $protocol;
    private $wsslug;
    private $entity;

    public function __construct($service_container) {
        $this->request = $service_container->get('request');
        $this->doctrine = $service_container->get('doctrine');
        $this->encoder = $service_container->get('security.encoder_factory');
    }

    public function setData($protocol, $wsslug) {
        $this->protocol = $protocol;
        $this->wsslug = $wsslug;
        $em = $this->doctrine->getManager();
        $this->entity = $em->getRepository('WebServiceBundle:WebService')->findOneBy(array(
            'slug' => $this->wsslug,
        ));
        return $this;
    }

    public function setEntity($protocol, $entity) {
        $this->protocol = $protocol;
        $this->entity = $entity;
        $this->wsslug = $this->entity->getSlug();
        return $this;
    }

    public function isCorrect() {
        $em = $this->doctrine->getManager();

        if ($this->entity->getIsToken()) {
            try {
                $token = "";
                switch ($this->protocol) {
                    case 'SOAP':
                        $content = new \DOMDocument();
                        $content->loadXML($this->request->getContent());
                        $token = $content->getElementsByTagName('token')->item(0)->nodeValue;
                        break;
                    case 'REST':
                        $token = $this->request->headers->get('token');
                        break;
                }
                
                $result = $em->getRepository('WebServiceBundle:WebService')->findWebServiceToken($this->wsslug, $this->protocol, $token);
                if (!$result) {
                    throw new \Exception('Bad credentials: token not allowed.');
                }
            } catch (\Exception $e) {
                throw new \Exception('Bad credentials: token not allowed.');
            }
        }
        
        if ($this->entity->getIsUser()) {
            try {
                $user = "";
                $pass = "";
                switch ($this->protocol) {
                    case 'SOAP':
                        $content = new \DOMDocument();
                        $content->loadXML($this->request->getContent());
                        $user = $content->getElementsByTagName('user')->item(0)->nodeValue;
                        $pass = $content->getElementsByTagName('pass')->item(0)->nodeValue;
                        break;
                    case 'REST':
                        $user = $this->request->headers->get('user');
                        $pass = $this->request->headers->get('pass');
                        break;
                }
                /*
                 * Verificar forma correcta de tomar el user y pass en el header de REST.
                 */


                /* Buscar el user y comprobar la contrasenna que envian con la codificada en BD */
                $usuario = $em->getRepository('WebServiceBundle:Users')->findOneBy(array(
                    'username'=>$user
                ));

                $encoder = $this->encoder->getEncoder($usuario);
                $passwordValid = $encoder->isPasswordValid($usuario->getPassword(), $pass ,$usuario->getSalt());
                if($passwordValid){
                    $result = $em->getRepository('WebServiceBundle:WebService')->findWebServiceUserPass($this->wsslug, $this->protocol, $user);
                    if (!$result) {
                        throw new \Exception('Bad credentials: token not allowed.');
                    }
                }else{
                    throw new \Exception('Bad credentials: User or Password incorrect.');
                }

            } catch (\Exception $e) {
                throw new \Exception('Bad credentials: User or Password incorrect.');
            }
        }
    }

}

?>
