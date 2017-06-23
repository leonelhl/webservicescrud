<?php

namespace Xalix\WebServiceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\Common\Collections\ArrayCollection;
use Xalix\WebServiceBundle\Entity\Atribute;
use Xalix\WebServiceBundle\Entity\Type;
use Xalix\WebServiceBundle\Form\TypeType;

/**
 * Type controller.
 *
 */
class TypeController extends Controller {

    /**
     * Lists all Type entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm2();
        $primaryType = $em->getRepository('WebServiceBundle:Type')->findBy(array(
            'isComplexType' => false));
        $secondType = $em->getRepository('WebServiceBundle:Type')->findBy(array(
            'isComplexType' => true));
        return $this->render('WebServiceBundle:Type:index.html.twig', array(
                    'entities1' => $primaryType,
                    'entities2' => $secondType,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a new Type entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new Type();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setIsComplexType(true);
            $entity->setIsArray(false);
            $entityArray = new Type();
            $entityArray->setType($entity->getType() . '[]');
            $entityArray->setIsComplexType(true);
            $entityArray->setIsArray(true);
            $em->persist($entity);
            $em->persist($entityArray);
            $em->flush();

            return $this->redirect($this->generateUrl('type_show', array('id' => $entity->getId())));
        }

        return $this->render('WebServiceBundle:Type:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Type entity.
     *
     * @param Type $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Type $entity) {
        $form = $this->createForm(new TypeType($this->get('translator')), $entity, array(
            'action' => $this->generateUrl('type_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Type entity.
     *
     */
    public function newAction() {
        $entity = new Type();
        $form = $this->createCreateForm($entity);

        return $this->render('WebServiceBundle:Type:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Type entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebServiceBundle:Type')->find($id);
        $atributes = $em->getRepository('WebServiceBundle:Type')->findAtributes($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Type entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebServiceBundle:Type:show.html.twig', array(
                    'entity' => $entity,
                    'atributes' => $atributes,
                    'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to edit an existing Type entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebServiceBundle:Type')->find($id);
        $entityArray = $em->getRepository('WebServiceBundle:Type')->findOneBy(array(
            'type' => $entity->getType() . '[]'
        ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Type entity.');
        }

        if (!$entity->getIsComplexType()) {
            // throw $this->createNotFoundException('Los tipos de datos por defecto no permiten modificaciones.');
            throw new Exception('Los tipos de dato por defecto no permiten modificaciones.');
        }

        //Buscando donde está siendo usado el tipo de dato.
        $function = $em->getRepository('WebServiceBundle:Type')->findInUseFunction($id);
        $param = $em->getRepository('WebServiceBundle:Type')->findInUseParam($id);
        $atribute = $em->getRepository('WebServiceBundle:Type')->findInUseAtribute($id);

        //Buscando donde está siendo usado el tipo de dato.Array
        $functionA = $em->getRepository('WebServiceBundle:Type')->findInUseFunction($entityArray->getId());
        $paramA = $em->getRepository('WebServiceBundle:Type')->findInUseParam($entityArray->getId());
        $atributeA = $em->getRepository('WebServiceBundle:Type')->findInUseAtribute($entityArray->getId());
        
        if ($function || $param || $atribute || $functionA || $paramA || $atributeA) {
            $this->get('session')->getFlashBag()->add('error', $entity->getType());
            return $this->redirect($this->generateUrl('type'));
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('WebServiceBundle:Type:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Type entity.
     *
     * @param Type $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Type $entity) {
        $form = $this->createForm(new TypeType($this->get('translator')), $entity, array(
            'action' => $this->generateUrl('type_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Editar'));

        return $form;
    }

    /**
     * Edits an existing Type entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebServiceBundle:Type')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Type entity.');
        }
//Nombre actual de la entidad a modificar
        $type = $entity->getType();

        $originalTags = new ArrayCollection();
// Create an ArrayCollection of the current Tag objects in the database
        foreach ($entity->getAtribute() as $tag) {
            $originalTags->add($tag);
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            //Si cambió el nombre de la entidad, se deberia cambiar el nombre
            //del arreglo creado automáticamente.
            if ($type != $entity->getType()) {
                $array = $em->getRepository('WebServiceBundle:Type')->findOneBy(array(
                    'type' => $type . '[]'
                ));
                $array->setType($entity->getType() . '[]');
            }
            // remove the relationship between the tag and the Task
            foreach ($originalTags as $tag) {
                if (false === $entity->getAtribute()->contains($tag)) {
                    $em->remove($tag);
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('type_show', array('id' => $id)));
        }

        return $this->render('WebServiceBundle:Type:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Type entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebServiceBundle:Type')->find($id);
            $entityArray = $em->getRepository('WebServiceBundle:Type')->findOneBy(array(
                'type' => $entity->getType() . '[]'
            ));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Type entity.');
            }
            if (!$entity->getIsComplexType()) {
                throw new Exception('Los tipos de dato por defecto no deben ser eliminados.');
            }
            try {
                $em->remove($entity);
                $em->remove($entityArray);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', $entity->getType());
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', $entity->getType());
            }
        }

        return $this->redirect($this->generateUrl('type'));
    }

    /**
     * Creates a form to delete a Type entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('type_delete', array('id' => $id)))
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

}
