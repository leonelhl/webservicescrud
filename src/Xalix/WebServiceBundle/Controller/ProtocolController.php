<?php

namespace Xalix\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Xalix\WebServiceBundle\Util\UpdateRest;

/**
 * Protocol controller.
 *
 */
class ProtocolController extends Controller {

    /**
     * Lists all Protocol entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $protocol = $em->getRepository('WebServiceBundle:Protocol')->findAllProtocol();

        return $this->render('WebServiceBundle:Protocol:index.html.twig', array(
                    'protocol' => $protocol,
        ));
    }

    /**
     * Able or Unable a Protocol.
     *
     */
    public function configureAction($id, $action) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebServiceBundle:Protocol')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Protocol entity.');
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
        if ($entity->getName() == 'REST') {
            $dir = $this->container->getParameter('dir');
            $rest = new UpdateRest();
            $em = $this->getDoctrine()->getManager();
            $rest->updatePublishRestServices($em,$dir);
        }

        return $this->redirect($this->generateUrl('protocol'));
    }

}
