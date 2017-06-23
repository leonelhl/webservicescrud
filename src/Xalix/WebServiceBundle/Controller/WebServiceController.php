<?php

namespace Xalix\WebServiceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Xalix\WebServiceBundle\Entity\WebService;
use Xalix\WebServiceBundle\Entity\WsFunction;
use Xalix\WebServiceBundle\Entity\Param;
use Xalix\WebServiceBundle\Entity\Contrate;
use Xalix\WebServiceBundle\Form\WebServiceType;
use Xalix\WebServiceBundle\Form\SecurityType;
use Doctrine\Common\Collections\ArrayCollection;
use Xalix\WebServiceBundle\Util\Util;
use Xalix\WebServiceBundle\Util\UpdateRest;

/**
 * WebService controller.
 *
 */
class WebServiceController extends Controller {

    /**
     * Lists all WebService entities.
     *
     */
    public function indexAction() {

        $em = $this->getDoctrine()->getManager();

        $paginator = $this->get('ideup.simple_paginator');

        $paginator->setItemsPerPage(10);
// Ahora sólo se muestran 5 números de página en el paginador
        $paginator->setMaxPagerItems(8);

        $entities = $paginator->paginate($em->getRepository('WebServiceBundle:WebService')->queryAllWebService())->getResult();

//        $entities = $em->getRepository('WebServiceBundle:WebService')->findAllWebService();
        $deleteForm = $this->createDeleteForm2();
// $entities= array();
        return $this->render('WebServiceBundle:WebService:index.html.twig', array(
                    'entities' => $entities,
                    'delete_form' => $deleteForm->createView(),
                    'paginador' => $paginator,
                    'search' => false,
        ));
    }

    public function configureAction($id, $action) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebServiceBundle:WebService')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Protocol entity.');
        }

        $is_REST = false;
        foreach ($entity->getProtocol() as $p) {
            if ($p->getName() == 'REST') {
                $is_REST = true;
            }
        }

        switch ($action) {
            case 'able':
                $entity->setIsActive(true);
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('able', $entity->getName());
                break;
            case 'unable':
                $entity->setIsActive(false);
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('unable', $entity->getName());
                break;
            default:
                throw $this->createNotFoundException('Ilegal action');
        }

        if ($is_REST) {
            $dir = $this->container->getParameter('dir');
            UpdateRest::updatePublishRestServices($em, $dir);
        }

        return $this->redirect($this->generateUrl('ws'));
    }

    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('ideup.simple_paginator');

        $paginator->setItemsPerPage(10);
// Ahora sólo se muestran 5 números de página en el paginador
        $paginator->setMaxPagerItems(8);
        $search = true;
        if (strlen($request->get('q')) == 0) {
            $entities = $paginator->paginate($em->getRepository('WebServiceBundle:WebService')->queryAllWebService())->getResult();
            $search = false;
        } else {
            $entities = $paginator->paginate($em->getRepository('WebServiceBundle:WebService')->queryAllSearchWebService($request->get('q')))->getResult();
            $search = true;
        }

        $deleteForm = $this->createDeleteForm2();

        return $this->render('WebServiceBundle:WebService:index.html.twig', array(
                    'entities' => $entities,
                    'delete_form' => $deleteForm->createView(),
                    'paginador' => $paginator,
                    'search' => $search,
        ));
    }

    /*
     * Leyendo el XML del WSDL para construir un objeto webservice
     */

    private function generateWebServiceByContrate($contrate) {
        $webservice = new WebService();
        $contrate = new \SimpleXMLElement($contrate);
//Contruyendo un objeto servicio web a partir de un contrato
        if ($contrate->types) {
            $webservice->setName($contrate->service['name'])
                    ->setUri($contrate->xpath('//soap:address')[0]['location'])
                    ->setIsActive(true)
                    ->setDescription($contrate->service->documentation);

            foreach ($contrate->portType->operation as $function) {
                $em = $this->getDoctrine()->getManager();
                $func = new WsFunction();
                $func->setName($function['name'])
                        ->setDescription($function->documentation);

                foreach ($contrate->message as $param) {
                    if ($param['name'] == $function['name'] . 'Request') {
                        foreach ($param->part as $p) {
                            $parameter = new Param();
                            $parameter->setName($p['name']);
                            $type = split(':', $p['type']);
                            $find_type = $em->getRepository('WebServiceBundle:Type')->findOneBy(array(
                                'type' => $type[1]
                            ));
                            $parameter->setType($find_type);
                            $func->addParam($parameter);
                        }
                    }
                    if ($param['name'] == $function['name'] . 'Response') {
                        $type = split(':', $param->part['type']);
                        $find_type = $em->getRepository('WebServiceBundle:Type')->findOneBy(array(
                            'type' => $type[1]
                        ));
                        $func->setReturn($find_type);
                    }
                }
                $webservice->addWsfunction($func);
            }
        } else {

            $webservice->setName($contrate->resources['name'])
                    ->setUri($contrate->resources['base'])
                    ->setIsActive(true)
                    ->setDescription($contrate->doc);

            foreach ($contrate->resources->resource as $function) {
                $em = $this->getDoctrine()->getManager();
                $func = new WsFunction();
                $uno = str_replace("/", '', $function['path']);
                $name = str_replace(".{_format}", '', $uno);
                $func->setName($name)
                        ->setDescription($function->doc);
                $method = $em->getRepository('WebServiceBundle:Method')->findOneBy(array('name' => $function->method['name']));
                $func->setMethod($method);

                if ($function->method->request->param) {
                    foreach ($function->method->request->param as $param) {
                        $parameter = new Param();
                        $parameter->setName($param['name']);
                        $type = split(':', $param['type']);
                        $find_type = $em->getRepository('WebServiceBundle:Type')->findOneBy(array(
                            'type' => $type[1]
                        ));
                        $parameter->setType($find_type);
                        $func->addParam($parameter);
                    }
                }
                if ($function->method->request->representation) {
                    foreach ($function->method->request->representation as $param) {
                        $parameter = new Param();
                        $parameter->setName($param['name']);
                        $type = "";
                        if ($param['mediaType'] == 'text/json') {
                            $type = 'anyJSON';
                        }
                        if ($param['mediaType'] == 'text/xml') {
                            $type = 'anyXML';
                        }
                        $find_type = $em->getRepository('WebServiceBundle:Type')->findOneBy(array(
                            'type' => $type
                        ));
                        $parameter->setType($find_type);
                        $func->addParam($parameter);
                    }
                }

                $find_type = "";
                if (strlen($function->method->response->param['type']) != 0) {
                    $type = $function->method->response->param['type'];
                    $type = split(':', $function->method->response->param['type']);
                    $find_type = $em->getRepository('WebServiceBundle:Type')->findOneBy(array(
                        'type' => $type[1]
                    ));
                }
                if (strlen($function->method->response->representation['mediaType']) != 0) {
                    $type = $function->method->response->representation['mediaType'];

                    if ($type == 'text/json') {
                        $type = 'anyJSON';
                    }
                    if ($type == 'text/xml') {
                        $type = 'anyXML';
                    }
                    $find_type = $em->getRepository('WebServiceBundle:Type')->findOneBy(array(
                        'type' => $type
                    ));
                }
                $func->setReturn($find_type);
                $webservice->addWsfunction($func);
            }
        }
        return $webservice;
    }

    public function editGenerateContrateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebServiceBundle:WebService')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WebService entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        return $this->generateContrate($editForm, $entity, 'edit', $request);
    }

    public function generateContrateAction(Request $request) {
        $entity = new WebService();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        return $this->generateContrate($form, $entity, 'new', $request);
    }

    /*
     * Función para dar formato a un XML. Para utilizarlo con el WSDL pues al 
     * ser generado usando twig se descompone la indentación
     */

    private function formatXML($xml) {
        $dom = new \DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($xml);
        $dom->formatOutput = TRUE;
        return $dom->saveXml();
    }

    /*
     * Genera los contratos para un servicio web y no permite que el usuario
     * cree un contrato si los datos no estan correctos
     */

    private function generateContrate($form, $entity, $action, $request) {

        foreach ($entity->getWsFunction() as $f) {
            $i = 0;
            foreach ($entity->getWsFunction() as $wf) {
                if ($f->getName() == $wf) {
                    $i+=1;
                    if ($i > 1) {
                        break;
                    }
                }
            }
            if ($i > 1) {
                $form->addError(new FormError($this->get('translator')->trans('message.equalfunc')));
                break;
            }
        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $wsdl = null;
            $wadl = null;
            $port = "";
            // $context = $this->get('router')->getContext();
            if ($request->getPort() != '80') {
                if ($request->isSecure()) {
                    if ($request->getPort() != '443') {
                        $port = ':' . $request->getPort();
                    }
                } else {
                    $port = ':' . $request->getPort();
                }
            }

            $entity->setUri($request->getScheme() . '://' . $request->getHost() . $port . $request->getBaseUrl() . '/webservices/' . $entity->getSlug());

//Seleccionando los tipos de datos de la funcion para generar el contrato
            $arrayType = array();
            foreach ($entity->getWsFunction() as $f) {
                if ($f->getReturn()->getIsComplexType() || $f->getReturn()->getIsArray()) {
                    $arrayType[$f->getReturn()->getId()] = $f->getReturn();
                    if ($f->getReturn()->getIsComplexType() && $f->getReturn()->getIsArray()) {
                        $type = $em->getRepository('WebServiceBundle:Type')->find($f->getReturn()->getId() - 1);
                        $arrayType[$type->getId()] = $type;
                    }
                }
                foreach ($f->getParam() as $p) {
                    if ($p->getType()->getIsComplexType() || $p->getType()->getIsArray()) {
                        $arrayType[$p->getType()->getId()] = $p->getType();
                        if ($p->getType()->getIsComplexType() && $p->getType()->getIsArray()) {
                            $type = $em->getRepository('WebServiceBundle:Type')->find($p->getType()->getId() - 1);
                            $arrayType[$type->getId()] = $type;
                        }
                    }
                }
            }
//Seleccionando los atributos de los tipos de datos complejos para generar el contrato
            foreach ($arrayType as $t) {
                if ($t->getIsComplexType()) {
                    foreach ($t->getAtribute() as $a) {
                        if ($a->getType()->getIsComplexType() || $a->getType()->getIsArray()) {
                            $arrayType[$a->getType()->getId()] = $a->getType();
                            if ($a->getType()->getIsComplexType() && $a->getType()->getIsArray()) {
                                $type = $em->getRepository('WebServiceBundle:Type')->find($a->getType()->getId() - 1);
                                $arrayType[$type->getId()] = $type;
                            }
                        }
                    }
                }
            }

            foreach ($entity->getProtocol() as $p) {
                if ($p->getName() == 'SOAP') {
                    $wsdl = $this->render('WebServiceBundle:WebService:wsdl.xml.twig', array('entity' => $entity, 'types' => $arrayType))->getContent();
                    $wsdl = $this->formatXML($wsdl);
                    $contrate = new Contrate();
                    $contrate->setFile($entity->getSlug() . '.wsdl')
                            ->setProtocol($p);
                    $entity->addContrate($contrate);
                }
                if ($p->getName() == 'REST') {
                    $wadl = $this->render('WebServiceBundle:WebService:wadl.xml.twig', array('entity' => $entity, 'types' => $arrayType))->getContent();
                    $wadl = $this->formatXML($wadl);
                    $contrate = new Contrate();
                    $contrate->setFile($entity->getSlug() . '.wadl')
                            ->setProtocol($p);
                    $entity->addContrate($contrate);
                }
            }

            $way = '';
            $form2 = null;
            if ($action == 'edit') {
                $form2 = $this->createEditForm($entity);
                $way = 'edit';
            } else {
                $form2 = $this->createCreateForm($entity);
                $way = 'new';
            }

            return $this->render('WebServiceBundle:WebService:contrate.html.twig', array(
                        'entity' => $entity,
                        'wsdl' => $wsdl,
                        'wadl' => $wadl,
                        'form' => $form2->createView(),
                        'way' => $way,
            ));
        }

        if ($action == 'edit') {
            $form_action = $this->generateUrl('ws_update', array('id' => $entity->getId()));
            $gen_action = $this->generateUrl('ws_edit_generate_contrate', array('id' => $entity->getId()));

            return $this->render('WebServiceBundle:WebService:edit.html.twig', array(
                        'entity' => $entity,
                        'edit_form' => $form->createView(),
                        'form_action' => $form_action,
                        'gen_action' => $gen_action,
            ));
        } else {
            $form_action = $this->generateUrl('ws_create');
            $gen_action = $this->generateUrl('ws_generate_contrate');

            return $this->render('WebServiceBundle:WebService:new.html.twig', array(
                        'entity' => $entity,
                        'form' => $form->createView(),
                        'form_action' => $form_action,
                        'gen_action' => $gen_action,
            ));
        }
    }

    /**
     * Finds and displays a WebService entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebServiceBundle:WebService')->findOneWebService($id);
        $function = $em->getRepository('WebServiceBundle:WebService')->findFunctionsWebService($id);

// $function = array();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WebService entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

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
                    'delete_form' => $deleteForm->createView(),
                    'delete' => true));
    }

    /**
     * Creates a new WebService entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new WebService();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $is_REST = false;
        $is_SOAP = false;

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            /*   if (count($entity->getProtocol()) == 1) {
              if ($entity->getProtocol()[0]->getName() == "SOAP") {
              foreach ($entity->getWsFunction() as $key) {
              foreach ($key->getMethod() as $m) {
              $key->removeMethod($m);
              }
              }
              }
              } */
            foreach ($entity->getContrate() as $c) {
                //Seleccionando los tipos de datos de la funcion para generar el contrato
                $arrayType = array();
                foreach ($entity->getWsFunction() as $f) {
                    if ($f->getReturn()->getIsComplexType() || $f->getReturn()->getIsArray()) {
                        $arrayType[$f->getReturn()->getId()] = $f->getReturn();
                        if ($f->getReturn()->getIsComplexType() && $f->getReturn()->getIsArray()) {
                            $type = $em->getRepository('WebServiceBundle:Type')->find($f->getReturn()->getId() - 1);
                            $arrayType[$type->getId()] = $type;
                        }
                    }
                    foreach ($f->getParam() as $p) {
                        if ($p->getType()->getIsComplexType() || $p->getType()->getIsArray()) {
                            $arrayType[$p->getType()->getId()] = $p->getType();
                            if ($p->getType()->getIsComplexType() && $p->getType()->getIsArray()) {
                                $type = $em->getRepository('WebServiceBundle:Type')->find($p->getType()->getId() - 1);
                                $arrayType[$type->getId()] = $type;
                            }
                        }
                    }
                }
//Seleccionando los atributos de los tipos de datos complejos para generar el contrato
                foreach ($arrayType as $t) {
                    if ($t->getIsComplexType()) {
                        foreach ($t->getAtribute() as $a) {
                            if ($a->getType()->getIsComplexType() || $a->getType()->getIsArray()) {
                                $arrayType[$a->getType()->getId()] = $a->getType();
                                if ($a->getType()->getIsComplexType() && $a->getType()->getIsArray()) {
                                    $type = $em->getRepository('WebServiceBundle:Type')->find($a->getType()->getId() - 1);
                                    $arrayType[$type->getId()] = $type;
                                }
                            }
                        }
                    }
                }

                if ($c->getProtocol()->getName() == 'SOAP') {
                    $is_SOAP = true;
                    $wsdl = $this->render('WebServiceBundle:WebService:wsdl.xml.twig', array('entity' => $entity, 'types' => $arrayType))->getContent();
                    $wsdl = $this->formatXML($wsdl);
                    try {
                        $this->generatePhpClass($this->generateWebServiceByContrate($wsdl), 'SOAP');
                        file_put_contents(__DIR__ . '/../WebServices/Contrates/soap/' . $c->getFile(), $wsdl);
                    } catch (\Exception $e) {
                        return $this->redirect($this->generateUrl('ws_new', array('id' => $entity->getId())));
                        $this->get('session')->getFlashBag()->add('error', $entity->getName());
                    }
                }
                if ($c->getProtocol()->getName() == 'REST') {
                    $is_REST = true;
                    $wadl = $this->render('WebServiceBundle:WebService:wadl.xml.twig', array('entity' => $entity, 'types' => $arrayType))->getContent();
                    $wadl = $this->formatXML($wadl);

                    try {
                        //$this->generatePhpClass($entity, 'REST');
                        $this->generatePhpClass($this->generateWebServiceByContrate($wadl), 'REST');
                        file_put_contents(__DIR__ . '/../WebServices/Contrates/rest/' . $c->getFile(), $wadl);
                    } catch (\Exception $e) {
                        return $this->redirect($this->generateUrl('ws_new', array('id' => $entity->getId())));
                        $this->get('session')->getFlashBag()->add('error', $entity->getName());
                    }
                }
            }
            $em->persist($entity);
            $em->flush();

            if (!$is_SOAP) {
                //$this->generatePhpClass($entity, 'SOAP');
                $this->generatePhpClass($this->generateWebServiceByContrate($wadl), 'SOAP');
            }
            if ($is_REST) {
                $dir = $this->container->getParameter('dir');
                UpdateRest::updatePublishRestServices($em, $dir);
            }

            return $this->redirect($this->generateUrl('ws_show', array('id' => $entity->getId())));
        }

        return $this->render('WebServiceBundle:WebService:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    private function generatePhpClass($webservice, $protocol) {
        $control = "";
        $method = "";
        $annotations = "";
        $action = "";
        $use = "";
        $extends = "";
        $namespacedir = 'ServicesClass';

        if ($protocol == 'REST') {
            $control = 'Controller';
            $action = 'Action';
            $namespacedir = 'RESTControllers';
            $use .= 'use FOS\RestBundle\Controller\FOSRestController;' . "\n";
            $use .= 'use FOS\RestBundle\Controller\Annotations;' . "\n";
            $use .= 'use Symfony\Component\HttpFoundation\Request;' . "\n";
            $use .= 'use Symfony\Component\HttpFoundation\Response;' . "\n";
            $use .= "\n";
            $use .= 'use Xalix\WebServiceBundle\WebServices\ServicesClass\\' . $webservice->getSlug() . ';' . "\n";
            $use .= "\n";
            $extends = ' extends FOSRestController';
        }

        $return = '<?php' . "\n";
        $return .= "\n";
        $return .= 'namespace Xalix\WebServiceBundle\WebServices\\' . $namespacedir . ';' . "\n";
        $return .= "\n";
        $return .= $use;
        $return .= '/**' . "\n";
        $return .= ' * WebService ' . $webservice->getName() . "\n";
        if (!strlen($webservice->getDescription()) == 0) {
            $return .= ' * ' . $webservice->getDescription() . "\n";
        } else {
            $return .= ' * ' . "\n";
        }
        $return .= ' */' . "\n";
        $return .= "class " . $webservice->getSlug() . $control . $extends . " {\n";
        $return .= "\n";
        foreach ($webservice->getWsFunction() as $f) {
            $implement = '/* Implement your code here */';

            if ($protocol == 'REST') {
                $method = ucfirst(Util::getSlug($f->getMethod()->getName()));
                $annotations = "    " . ' * @Annotations\\' . $method . '("/' . $f->getName() . '")' . "\n";
            }

            $parameterAnnotation = "";
            $parameterList = "";
            $i = 0;

            $call = "";

            foreach ($f->getParam() as $p) {
                if ($i > 0) {
                    $parameterList .= ', $' . $p->getName();
                    $call.=', $request->get(' . "'" . $p->getName() . "'" . ')';
                } else {
                    $parameterList .= '$' . $p->getName();
                    $call.='$request->get(' . "'" . $p->getName() . "'" . ')';
                }
                $parameterAnnotation.= "\n" . '     * @param ' . $p->getType() . ' $' . $p->getName();
                $i = $i + 1;
            }

            if ($protocol == 'REST') {
                $implement = "";
                $implement .= '$this->container->get("service.security")->setData("REST", "' . $webservice->getSlug() . '")->isCorrect();' . "\n";
                $implement .= "\t" . '$obj = new ' . $webservice->getSlug() . '();' . "\n";
                $implement .= "\t" . 'return $obj->' . $f->getName() . '(' . $call . ');';
            }

            if ($protocol == 'REST') {
                if (count($f->getParam()) == 0) {
                    $parameterList = '';
                } else {
                    $parameterList = 'Request $request';
                }
            }

            if (!strlen($f->getDescription()) == 0) {
                $return .= "    " . '/**' . "\n";
                $return .= "    " . ' * ' . $f->getDescription() . "\n";
                $return .= "    " . ' * ';
            } else {
                $return .= "    " . '/**';
            }
            if (!strlen($parameterAnnotation) == 0) {
                $return .= "    " . $parameterAnnotation . "\n";
            } else {
                $return .= "\n" . "     *" . "\n";
            }
            $return .= "    " . ' * @return ' . $f->getReturn() . "\n";
            $return .= $annotations;
            $return .= "    " . ' */' . "\n";
            $return .= "    " . 'public function ' . $f->getName() . $action . '(' . $parameterList . ') {' . "\n";
            $return .= "\t" . $implement . "\n";
            $return .= "    " . '}' . "\n";
            $return .= "\n";
        }
        if ($protocol == 'REST') {
            $return .= "    " . 'public function getWadlAction () {' . "\n";
            $return .= "\t" . 'return new Response(file_get_contents(__DIR__ . "' . '/../Contrates/rest/' . $webservice->getSlug() . '.wadl"));' . "\n";
            $return .= "    " . '}' . "\n";
            $return .= "\n";
        }
        $return .= '}' . "\n";
        $return .= "\n";
        $return .= '?>';
        $class = $webservice->getClass();
        $dir = 'ServicesClass';
        if ($protocol == 'REST') {
            $class = $webservice->getSlug() . 'Controller.php';
            $dir = 'RESTControllers';
        }
        try {
            file_put_contents(__DIR__ . '/../WebServices/' . $dir . '/' . $class, $return);
        } catch (\Exception $e) {
            throw new \Exception('Permisions denied in /WebServices folder in WebServiceBundle');
        }
    }

    /**
     * Creates a form to create a WebService entity.
     *
     * @param WebService $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(WebService $entity) {
        $form = $this->createForm(new WebServiceType($this->get('translator')), $entity, array(
            'action' => $this->generateUrl('ws_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new WebService entity.
     *
     */
    public function newAction() {
        $entity = new WebService();
        $form = $this->createGenerateCForm($entity);
        $form_action = "";
        $gen_action = "";

        return $this->render('WebServiceBundle:WebService:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'form_action' => $form_action,
                    'gen_action' => $gen_action,
        ));
    }

    private function createGenerateCForm(WebService $entity) {
        $form = $this->createForm(new WebServiceType($this->get('translator')), $entity, array(
            'action' => $this->generateUrl('ws_generate_contrate'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to edit an existing WebService entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebServiceBundle:WebService')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WebService entity.');
        }

        $editForm = $this->createGenerateEForm($entity);

        $form_action = "";
        $gen_action = "";

        return $this->render('WebServiceBundle:WebService:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'form_action' => $form_action,
                    'gen_action' => $gen_action,
        ));
    }

    private function createGenerateEForm(WebService $entity) {
        $form = $this->createForm(new WebServiceType($this->get('translator')), $entity, array(
            'action' => $this->generateUrl('ws_edit_generate_contrate', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Creates a form to edit a WebService entity.
     *
     * @param WebService $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(WebService $entity) {
        $form = $this->createForm(new WebServiceType($this->get('translator')), $entity, array(
            'action' => $this->generateUrl('ws_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing WebService entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebServiceBundle:WebService')->find($id);

        $name_pre = $entity->getName();
        $class_pre = $entity->getClass();
        $contrat_file_pre = array();
        $slug_pre = $entity->getSlug();
        $is_REST = false;
        $is_SOAP = false;

        foreach ($entity->getContrate() as $con) {
            $contrat_file_pre[$con->getProtocol()->getName()] = $con->getFile();
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WebService entity.');
        }

        $originalContrates = new ArrayCollection();
        foreach ($entity->getContrate() as $tag) {
            $originalContrates->add($tag);
        }

        $originalFunctions = new ArrayCollection();
        $originalParams = new ArrayCollection();
// Create an ArrayCollection of the current Tag objects in the database
        foreach ($entity->getWsFunction() as $tag) {
            foreach ($tag->getParam() as $param) {
                $originalParams->add($param);
            }
            $originalFunctions->add($tag);
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

//Para quitar las funciones y los parametros q se liminaron en el editar
            foreach ($originalParams as $p) {
                foreach ($entity->getWsFunction() as $f) {
                    if ($p->getWsFunction() == $f) {
                        if (false === $f->getParam()->contains($p)) {
                            $em->remove($p);
                        }
                    }
                }
            }

            foreach ($originalFunctions as $tag) {
                if (false === $entity->getWsFunction()->contains($tag)) {
                    $em->remove($tag);
                }
            }

            foreach ($originalContrates as $con) {
                $entity->removeContrate($con);
                $em->remove($con);
            }
            $em->flush();

//****************************

            /*  if (count($entity->getProtocol()) == 1) {
              if ($entity->getProtocol()[0]->getName() == "SOAP") {

              foreach ($entity->getWsFunction() as $key) {
              $key->setMethod(null);
              }
              }
              } */
            foreach ($entity->getContrate() as $c) {
                $arrayType = array();
                //Seleccionando los tipos de datos de la funcion para generar el contrato
                foreach ($entity->getWsFunction() as $f) {
                    if ($f->getReturn()->getIsComplexType() || $f->getReturn()->getIsArray()) {
                        $arrayType[$f->getReturn()->getId()] = $f->getReturn();
                        if ($f->getReturn()->getIsComplexType() && $f->getReturn()->getIsArray()) {
                            $type = $em->getRepository('WebServiceBundle:Type')->find($f->getReturn()->getId() - 1);
                            $arrayType[$type->getId()] = $type;
                        }
                    }
                    foreach ($f->getParam() as $p) {
                        if ($p->getType()->getIsComplexType() || $p->getType()->getIsArray()) {
                            $arrayType[$p->getType()->getId()] = $p->getType();
                            if ($p->getType()->getIsComplexType() && $p->getType()->getIsArray()) {
                                $type = $em->getRepository('WebServiceBundle:Type')->find($p->getType()->getId() - 1);
                                $arrayType[$type->getId()] = $type;
                            }
                        }
                    }
                }
//Seleccionando los atributos de los tipos de datos complejos para generar el contrato
                foreach ($arrayType as $t) {
                    if ($t->getIsComplexType()) {
                        foreach ($t->getAtribute() as $a) {
                            if ($a->getType()->getIsComplexType() || $a->getType()->getIsArray()) {
                                $arrayType[$a->getType()->getId()] = $a->getType();
                                if ($a->getType()->getIsComplexType() && $a->getType()->getIsArray()) {
                                    $type = $em->getRepository('WebServiceBundle:Type')->find($a->getType()->getId() - 1);
                                    $arrayType[$type->getId()] = $type;
                                }
                            }
                        }
                    }
                }

                if ($c->getProtocol()->getName() == 'SOAP') {
                    $is_SOAP = true;
                    $wsdl = $this->render('WebServiceBundle:WebService:wsdl.xml.twig', array('entity' => $entity, 'types' => $arrayType))->getContent();
                    $wsdl = $this->formatXML($wsdl);
                    try {
//Se le debería colocal Old a la clase vieja en ves de reemplazarla pues se corre el riesgo 
// de perder código implementado.
                        if ($name_pre != $entity->getName()) {
                            @unlink(__DIR__ . '/../WebServices/Contrates/soap/' . $contrat_file_pre['SOAP']);
                        }

                        @rename(__DIR__ . '/../WebServices/ServicesClass/' . $class_pre, __DIR__ . '/../WebServices/ServicesClass/' . $class_pre . '[old]');

                        $this->generatePhpClass($this->generateWebServiceByContrate($wsdl), 'SOAP');
                        file_put_contents(__DIR__ . '/../WebServices/Contrates/soap/' . $c->getFile(), $wsdl);
                    } catch (\Exception $e) {
                        return $this->redirect($this->generateUrl('ws_edit', array('id' => $entity->getId())));
                        $this->get('session')->getFlashBag()->add('error', c);
                    }
                }
                if ($c->getProtocol()->getName() == 'REST') {
                    $is_REST = true;
                    $wadl = $this->render('WebServiceBundle:WebService:wadl.xml.twig', array('entity' => $entity, 'types' => $arrayType))->getContent();
                    $wadl = $this->formatXML($wadl);
                    try {
//Se le debería colocal Old a la clase vieja en ves de reemplazarla pues se corre el riesgo 
// de perder código implementado.
                        if ($name_pre != $entity->getName()) {

                            @unlink(__DIR__ . '/../WebServices/Contrates/rest/' . $contrat_file_pre['REST']);
                            @unlink(__DIR__ . '/../WebServices/RESTControllers/' . $slug_pre . 'Controller.php');
                        }
                        $this->generatePhpClass($this->generateWebServiceByContrate($wadl), 'REST');
                        //$this->generatePhpClass($entity, 'REST');
                        file_put_contents(__DIR__ . '/../WebServices/Contrates/rest/' . $c->getFile(), $wadl);
                    } catch (\Exception $e) {
                        return $this->redirect($this->generateUrl('ws_edit', array('id' => $entity->getId())));
                        $this->get('session')->getFlashBag()->add('error', $entity->getName());
                    }
                }
            }

            if (!$is_SOAP) {
                @rename(__DIR__ . '/../WebServices/ServicesClass/' . $class_pre, __DIR__ . '/../WebServices/ServicesClass/' . $class_pre . '[old]');
                @unlink(__DIR__ . '/../WebServices/Contrates/soap/' . $contrat_file_pre['SOAP']);
                $this->generatePhpClass($this->generateWebServiceByContrate($wadl), 'SOAP');
                // $this->generatePhpClass($entity, 'SOAP');
            }
            if (!$is_REST) {

                @unlink(__DIR__ . '/../WebServices/Contrates/rest/' . $contrat_file_pre['REST']);
                @unlink(__DIR__ . '/../WebServices/RESTControllers/' . $slug_pre . 'Controller.php');
            }

            $em->persist($entity);
            $em->flush();
            $dir = $this->container->getParameter('dir');
            UpdateRest::updatePublishRestServices($em, $dir);

            return $this->redirect($this->generateUrl('ws_show', array('id' => $id)));
        }

        return $this->render('WebServiceBundle:WebService:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a WebService entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebServiceBundle:WebService')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find WebService entity.');
            }
            /*
             * Falta implementar toda la lógica referente a 
             * qué hacer con los archivos generados por los Ws,
             * si borrarlos o moverlos a un backup.
             */
            try {
                $is_SOAP = false;
                $is_REST = false;
                foreach ($entity->getContrate() as $c) {
                    if ($c->getProtocol()->getName() == 'SOAP') {
                        $is_SOAP = true;
                        unlink(__DIR__ . '/../WebServices/Contrates/soap/' . $c->getFile());
                        unlink(__DIR__ . '/../WebServices/ServicesClass/' . $entity->getClass());
                    }
                    if ($c->getProtocol()->getName() == 'REST') {
                        $is_REST = true;
                        unlink(__DIR__ . '/../WebServices/Contrates/rest/' . $c->getFile());
                        unlink(__DIR__ . '/../WebServices/RESTControllers/' . $entity->getSlug() . 'Controller.php');
                    }
                }
                if (!$is_SOAP) {
                    unlink(__DIR__ . '/../WebServices/ServicesClass/' . $entity->getClass());
                }

                $em->remove($entity);
                $em->flush();
                if ($is_REST) {
                    $dir = $this->container->getParameter('dir');
                    UpdateRest::updatePublishRestServices($em, $dir);
                }
                $this->get('session')->getFlashBag()->add('info', $entity->getName());
            } catch (\Exception $e) {
                try {
                    $em->remove($entity);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('purge', $entity->getName());
                } catch (\Exception $e) {
//$this->get('session')->getFlashBag()->add('nopurge', $entity->getName());
                    $this->get('session')->getFlashBag()->add('error', $entity->getName());
                }
            }
        }

        return $this->redirect($this->generateUrl('ws'));
    }

    /**
     * Creates a form to delete a WebService entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('ws_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => $this->get('translator')->trans('modal.delete')))
                        ->getForm()
        ;
    }

    private function createDeleteForm2() {
        return $this->createFormBuilder()
//  ->setAction($this->generateUrl('ws_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => $this->get('translator')->trans('modal.delete'), 'attr' => array('class' => 'btn btn-danger',)))
                        ->getForm()
        ;
    }

    public function notavailableAction() {
        return $this->render('WebServiceBundle:WebService:notavailable.html.twig');
    }

    public function securityAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebServiceBundle:WebService')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WebService entity.');
        }

        $editForm = $this->createSecurityForm($entity);

        return $this->render('WebServiceBundle:WebService:security.html.twig', array(
                    'form' => $editForm->createView(),
                    'entity' => $entity,
        ));
    }

    private function createSecurityForm(WebService $entity) {
        $form = $this->createForm(new SecurityType($this->get('translator')), $entity, array(
            'action' => $this->generateUrl('ws_security_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    public function securityUpdateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebServiceBundle:WebService')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WebService entity.');
        }

        $editForm = $this->createSecurityForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ws_security', array('id' => $id)));
        }

        return $this->render('WebServiceBundle:WebService:security.html.twig', array(
                    'entity' => $entity,
                    'form' => $editForm->createView(),
        ));
    }

    public function updateuriAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $soapservice = $em->getRepository('WebServiceBundle:WebService')->findTypeService('SOAP');
        $restservice = $em->getRepository('WebServiceBundle:WebService')->findTypeService('REST');
        $route = $request->server->get('HTTP_REFERER');

        $port = "";
            // $context = $this->get('router')->getContext();
            if ($request->getPort() != '80') {
                if ($request->isSecure()) {
                    if ($request->getPort() != '443') {
                        $port = ':' . $request->getPort();
                    }
                } else {
                    $port = ':' . $request->getPort();
                }
            }

try{
       foreach ($soapservice  as $service) {
            $service->setUri($request->getScheme() . '://' . $request->getHost() . $port . $request->getBaseUrl() . '/webservices/' . $service->getSlug());
            $em->persist($service);
            $wsdl = $this->formatXML(file_get_contents(__DIR__ . '/../WebServices/Contrates/soap/' . $service->getSlug().'.wsdl'));
            $contrate = new \SimpleXMLElement($wsdl);
            $contrate->xpath('//soap:address')[0]['location'] = $service->getUri();
            file_put_contents(__DIR__ . '/../WebServices/Contrates/soap/' . $service->getSlug().'.wsdl', $contrate->saveXML());
        }

         foreach ($restservice  as $service) {
            $service->setUri($request->getScheme() . '://' . $request->getHost() . $port . $request->getBaseUrl() . '/webservices/' . $service->getSlug());
            $em->persist($service);
            $wadl = $this->formatXML(file_get_contents(__DIR__ . '/../WebServices/Contrates/rest/' . $service->getSlug().'.wadl'));

             $contrate = new \SimpleXMLElement($wadl);
             $contrate->resources['base'] = $service->getUri();
             file_put_contents(__DIR__ . '/../WebServices/Contrates/rest/' . $service->getSlug().'.wadl', $contrate->saveXML());
        }
        $em->flush();
        $dir = $this->container->getParameter('dir');
        UpdateRest::updatePublishRestServices($em, $dir);
        $this->get('session')->getFlashBag()->add('updateUriOk', 'Update Uri Ok');
    return $this->redirect($this->generateUrl('ws'));
}catch(\Exception $e){
         $this->get('session')->getFlashBag()->add('updateUriError', 'Update Uri Error');
    return $this->redirect($this->generateUrl('ws'));
}
        

    }

}
