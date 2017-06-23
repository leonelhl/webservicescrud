<?php
/**
 * Created by PhpStorm.
 * User: lachy
 * Date: 3/26/16
 * Time: 3:42 PM
 */

namespace Xalix\WebServiceBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class AccessOriginRepository extends EntityRepository
{

    public function findByUserDate($wsuser)
    {
        $em = $this->getEntityManager();
        $d = new \DateTime();
        $ds = '01/'.$d->format('m').'/'.$d->format('Y').' 00:00:00';
        $d = \DateTime::createFromFormat('d/m/Y H:i:s', $ds);
        $dn = \DateTime::createFromFormat('d/m/Y H:i:s', $ds)->modify('+1 month');

        $dql = 'SELECT p
        FROM WebServiceBundle:AccessOrigin p JOIN p.user u
        WHERE u = :wsuser AND p.accessDate > :fm';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('wsuser', $wsuser);
        $consulta->setParameter('fm', $d);

        return $consulta->getResult();
    }

}