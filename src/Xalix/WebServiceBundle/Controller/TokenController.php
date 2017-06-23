<?php

namespace Xalix\WebServiceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Xalix\WebServiceBundle\Controller\genericController;
use Xalix\WebServiceBundle\Entity\Token;
use Xalix\WebServiceBundle\Form\TokenType;

/**
 * Token controller.
 *
 */
class TokenController extends genericController
{
    protected $entity = 'WebServiceBundle:Token';

    protected $entityClass = Token::class;

    protected $entityForm = TokenType::class;

    protected $render_prefix = 'WebServiceBundle:Token:';

    protected $url_prefix = 'token';

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

return $this->redirect($this->generateUrl($this->url_prefix));
    }


}
