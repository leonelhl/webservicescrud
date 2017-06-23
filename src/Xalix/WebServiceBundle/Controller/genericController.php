<?php
/**
 * Created by PhpStorm.
 * User: lachy
 * Date: 10/8/15
 * Time: 7:32 AM
 */

namespace Xalix\WebServiceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class genericController extends Controller
{

    protected $entity;

    protected $entityClass;

    protected $entityForm;

    protected $render_prefix;

    protected $url_prefix;

    protected $generic_type;


    /**
     * Lists all Tipo entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


		if($request->get('filter')){
            $data = (array)json_decode($request->get('filter'));
			$entities = $this->filterQuery($data);//$query->getResult();
			
//			if($request->get('plano'))
//			{
//				$data_plano = [];
//				foreach($entities as $value){
//					$value->setPlano($request->get('plano'));
//				}
//			}
        
			//return new Response(json_encode($entities));
		}else{
			$entities = $em->getRepository($this->entity)->findAll();
		}
      

        if($request->get('limit')){
            $paginator = $this->get('ideup.simple_paginator');
            $paginator->setItemsPerPage($request->get('limit'));

            $entities = $paginator->paginate($entities)->getResult();
        }
        $entities = $this->beforeIndexResult($entities, $request, $em);
        $count = count($entities);
        if($request->get('format') == 'json' /*and $request->isXmlHttpRequest()*/){
            if($request->get('limit')){
                return new Response(json_encode(array('datas'=>$entities, 'total'=> $count)));
            }
            return new Response(json_encode($entities));
        }

        return $this->render($this->render_prefix.'index.html.twig', array(
            'entities' => $entities,
            'request' => $request
        ));
    }


    protected function filterQuery($data)
    {
        $em = $this->getDoctrine()->getManager();
        $filter = Array();
        $str = '';
        $count_filters = 1;
        $lenData = count($data);
        foreach($data as $key => $value){
            $filter[$key] = '%'.$value.'%';
            $str .= 'p.'.$key.' LIKE :'.$key.' ';

            if($count_filters < $lenData)
            {
                $str.='OR ';
            }
            $count_filters += 1;
        }

        $repository = $em->getRepository($this->entity);
        $query = $repository->createQueryBuilder('p')->where($str)->setParameters($filter)->orderBy('p.id', 'DESC')->getQuery();
        return $query->getResult();
    }

    protected function beforeIndexResult($objArray, $request, $em){
        return $objArray;
    }
    /**
     * Creates a new Tipo entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new $this->entityClass();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl($this->url_prefix, array('id' => $entity->getId())));
        }

        return $this->render($this->render_prefix.'new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Tipo entity.
     *
     * @param Tipo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $form = $this->createForm(new $this->entityForm(), $entity, array(
            'action' => $this->generateUrl($this->url_prefix.'_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Tipo entity.
     *
     */
    public function newAction()
    {
        $entity = new $this->entityClass();
        $form   = $this->createCreateForm($entity);

        return $this->render($this->render_prefix.'new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Tipo entity.
     *
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($this->entity)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tipo entity.');
        }

        if($request->get('format') == 'json'){
            return new Response(json_encode($entity));
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render($this->render_prefix.'show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Tipo entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($this->entity)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tipo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render($this->render_prefix.'new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Tipo entity.
     *
     * @param Tipo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        $form = $this->createForm(new $this->entityForm(), $entity, array(
            'action' => $this->generateUrl($this->url_prefix.'_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Tipo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($this->entity)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tipo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl($this->url_prefix, array('id' => $id)));
        }

        return $this->render($this->render_prefix.'new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Tipo entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
		$errors = array();
        if (true) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository($this->entity)->find($id);

            if (!$entity) {
                $errors[] = 'No se encuentra la entidad seleccionada!';
            }
            $em->remove($entity);
            $em->flush();
        }

        return new Response(json_encode(array('success'=> count($errors) == 0, 'Errors'=> $errors)));
    }

    /**
     * Creates a form to delete a Tipo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($this->url_prefix.'_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }
    
    public function filterByAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		$filter = array();
		$data = json_decode($request->get('filter'));
		if($request->get('filter')){
			foreach($data as $attr=>$val)
			{
				$filter[$attr] = $val;
			}
		}
		$entity = $em->getRepository($this->entity)->findBy($filter);
		
		return new Response(json_encode($entity));
    }
}
