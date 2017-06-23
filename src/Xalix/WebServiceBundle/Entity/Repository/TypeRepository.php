<?php

namespace Xalix\WebServiceBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class TypeRepository extends EntityRepository {

    public function findAtributes($id) {
        $em = $this->getEntityManager();
        $dql = 'SELECT a,t
                FROM WebServiceBundle:Atribute a
                JOIN a.complexType t
                WHERE t.id=:id
                ORDER BY a.id';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id', $id);

        return $consulta->getResult();
    }

    public function findInUseFunction($id) {
        $em = $this->getEntityManager();
        $dql = 'SELECT f,t
                FROM WebServiceBundle:WsFunction f
                JOIN f.return t
                WHERE t.id=:id';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id', $id);

        return $consulta->getResult();
    }

    
    public function findInUseParam($id) {
        $em = $this->getEntityManager();
        $dql = 'SELECT p,t
                FROM WebServiceBundle:Param p
                JOIN p.type t
                WHERE t.id=:id';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id', $id);

        return $consulta->getResult();
    }
    
     public function findInUseAtribute($id) {
        $em = $this->getEntityManager();
        $dql = 'SELECT a,t
                FROM WebServiceBundle:Atribute a
                JOIN a.type t
                WHERE t.id=:id';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id', $id);

        return $consulta->getResult();
    }
}

?>
