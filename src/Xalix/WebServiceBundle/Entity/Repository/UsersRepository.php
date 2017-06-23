<?php
/**
 * Created by PhpStorm.
 * User: lachy
 * Date: 4/13/16
 * Time: 10:53 PM
 */

namespace Xalix\WebServiceBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class UsersRepository extends EntityRepository
{

    public function findByList($value) {
        $em = $this->getEntityManager();
        $dql = 'SELECT u FROM WebServiceBundle:Users u
                WHERE u.username LIKE :gval OR
                u IN (SELECT xu FROM modeloBundle:Partner pr JOIN pr.xw_user xu WHERE pr.companyName LIKE :gval OR
                pr.plan IN (SELECT pl FROM modeloBundle:Partner ptr JOIN ptr.plan pl WHERE pl.nombre LIKE :gval)) OR
                u.cantidad_consultas LIKE :gval
                ORDER BY u.id DESC';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('gval', '%'.$value.'%');

        return $consulta->getResult();
    }

}