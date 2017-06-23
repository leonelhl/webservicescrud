<?php

namespace Xalix\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

class UddiController extends Controller {

    public function indexAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
                        'WebServiceBundle:UDDI:index.html.twig', array(
                    // last username entered by the user
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error' => $error,
                        )
        );
    }

    public function showallAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $em = $this->getDoctrine()->getManager();
        
//        $paginator = $this->get('ideup.simple_paginator');
//
//        $paginator->setItemsPerPage(15);
//// Ahora sólo se muestran 5 números de página en el paginador
//        $paginator->setMaxPagerItems(8);
//        $entities = $paginator->paginate($em->getRepository('WebServiceBundle:WebService')->queryUDDIWebService())->getResult();
        $entities = $em->getRepository('WebServiceBundle:WebService')->findUDDIWebService();

        //    $entities= array();
        return $this->render('WebServiceBundle:UDDI:showall.html.twig', array(
                    'entities' => $entities,
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error' => $error,
                    'search' => false,
        ));
    }

    public function searchAction(Request $request) {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('ideup.simple_paginator');

        $paginator->setItemsPerPage(10);
// Ahora sólo se muestran 5 números de página en el paginador
        $paginator->setMaxPagerItems(8);
        
         if(strlen($request->get('q'))==0){
               $entities = $paginator->paginate($em->getRepository('WebServiceBundle:WebService')->queryUDDIWebService())->getResult();
        }else{
            $entities = $paginator->paginate($em->getRepository('WebServiceBundle:WebService')->queryAllSearchUddiWebService($request->get('q')))->getResult();
        }
        
        return $this->render('WebServiceBundle:UDDI:showall.html.twig', array(
                    'entities' => $entities,
                    'paginador' => $paginator,
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error' => $error,
                    'search' => true,
        ));
    }

    /**
     * Finds and displays a WebService entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebServiceBundle:WebService')->findOneUDDIWebService($id);

        // $function = array();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WebService entity.');
        }

        $function = $em->getRepository('WebServiceBundle:WebService')->findFunctionsWebService($id);


        $wsdl = null;
        $wadl = null;
        //print_r($entity);
        foreach ($entity->getContrate() as $ent) {
            switch ($ent->getProtocol()) {
                case 'SOAP': $wsdl = $ent;
                    break;
                case 'REST': $wadl = $ent;
                    break;
            }
        }

        return $this->render('WebServiceBundle:WebService:show.html.twig', array(
                    'entity' => $entity,
                    'function' => $function,
                    'wsdl' => $wsdl,
                    'wadl' => $wadl,
                    'delete' => false));
    }

}
