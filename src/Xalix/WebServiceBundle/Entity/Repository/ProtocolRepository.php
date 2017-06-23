<?php

namespace Xalix\WebServiceBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ProtocolRepository extends EntityRepository {

    public function findAllProtocol() {
        $em = $this->getEntityManager();
        $dql = 'SELECT p
                FROM WebServiceBundle:Protocol p
                ORDER BY p.id';

        $consulta = $em->createQuery($dql);

        return $consulta->getResult();
    }

}

?>
