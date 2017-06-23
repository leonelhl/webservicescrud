<?php

namespace Xalix\WebServiceBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class WebServiceRepository extends EntityRepository {

    public function queryAllWebService() {
        /*
         * Mejorar esto para que no se active el lazy loading
         * además que el paginador pinche bien
         */
        //    $qb = $this->createQueryBuilder('w')
        //            ->select('w, p')
        //            ->innerJoin('w.protocol', 'p')
        //            ->orderBy('w.id');
        //return $qb->getQuery();
        $em = $this->getEntityManager();
        $dql = 'SELECT w
               FROM WebServiceBundle:WebService w
               ORDER BY w.id';

        $consulta = $em->createQuery($dql);

        return $consulta;
    }

    public function findAllWebService() {
        return $this->queryAllWebService()->getResult();
    }

    public function findOneWebServiceByProtocol($slug, $protocol) {
        $em = $this->getEntityManager();
        $dql = 'SELECT w,p
                FROM WebServiceBundle:WebService w
                JOIN w.protocol p
                WHERE w.slug = :slug AND p.name =:protocol';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('slug', $slug);
        $consulta->setParameter('protocol', $protocol);

        return $consulta->getSingleResult();
    }

    public function findOneWebService($id) {
        $em = $this->getEntityManager();
        $dql = 'SELECT w,p,c
                FROM WebServiceBundle:WebService w
                JOIN w.protocol p
                JOIN w.contrate c
                WHERE w.id = :id';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id', $id);

        return $consulta->getSingleResult();
    }

    public function findFunctionsWebService($id) {
        $em = $this->getEntityManager();
        $dql = 'SELECT w,f,t
                FROM WebServiceBundle:WsFunction f
                JOIN f.webservice w
                JOIN f.return t
                WHERE f.webservice = :id';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id', $id);

        return $consulta->getResult();
    }

    public function queryUDDIWebService() {
        $em = $this->getEntityManager();
        $dql = 'SELECT w,p 
                FROM WebServiceBundle:WebService w
                JOIN w.protocol p
                WHERE w.isActive = TRUE AND p.isActive = TRUE
                ORDER BY w.id';

        $consulta = $em->createQuery($dql);

        return $consulta;
    }

    public function findUDDIWebService() {
        return $this->queryUDDIWebService()->getResult();
    }

    public function findOneUDDIWebService($id) {
        $em = $this->getEntityManager();
        $dql = 'SELECT w,p,c
                FROM WebServiceBundle:WebService w
                JOIN w.protocol p
                JOIN w.contrate c
                WHERE p.isActive = TRUE AND w.isActive = TRUE AND w.id = :id';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id', $id);

        return $consulta->getSingleResult();
    }

    public function queryAllSearchWebService($value) {
        $em = $this->getEntityManager();
        $dql = "SELECT w,p
                FROM WebServiceBundle:WebService w
                JOIN w.protocol p
                WHERE w.name LIKE '%$value%' OR w.slug LIKE '%$value%' OR w.uri LIKE '%$value%' OR w.class LIKE '%$value%' OR w.description LIKE '%$value%' OR p.name LIKE '%$value%' OR p.description LIKE '%$value%'
                ORDER BY w.id";

        $consulta = $em->createQuery($dql);
        //$consulta->setParameter('value', $value);

        return $consulta;
    }

    public function findAllSearchWebService($value) {
        return $this->queryAllSearchWebService($value)->getResult();
    }

    public function queryAllSearchUddiWebService($value) {
        $em = $this->getEntityManager();
        $dql = "SELECT w,p
                FROM WebServiceBundle:WebService w
                JOIN w.protocol p
                WHERE w.isActive = TRUE AND p.isActive = TRUE AND w.name LIKE '%$value%' OR w.slug LIKE '%$value%' OR w.uri LIKE '%$value%' OR w.class LIKE '%$value%' OR w.description LIKE '%$value%' OR p.name LIKE '%$value%' OR p.description LIKE '%$value%'
                ORDER BY w.id";

        $consulta = $em->createQuery($dql);
        //  $consulta->setParameter('value', $value);

        return $consulta;
    }

    public function findAllSearchUddiWebService($value) {
        return $this->queryAllSearchUddiWebService($value)->getResult();
    }


    public function findTypeService($protocol) {
        $em = $this->getEntityManager();
        $dql = "SELECT w
                FROM WebServiceBundle:WebService w
                JOIN w.protocol p
                WHERE p.name = :protocol";

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('protocol', $protocol);

        return $consulta->getResult();
    }

    public function findRestServicesPublish() {
        $em = $this->getEntityManager();
        $dql = "SELECT w,p
                FROM WebServiceBundle:WebService w
                JOIN w.protocol p
                WHERE p.name = 'REST' AND p.isActive = TRUE AND w.isActive = TRUE";

        $consulta = $em->createQuery($dql);

        return $consulta->getResult();
    }

    public function findWebServiceToken($wsslug, $protocol, $token) {
        $em = $this->getEntityManager();
        $dql = 'SELECT w,p,t
                FROM WebServiceBundle:WebService w
                JOIN w.protocol p
                JOIN w.token t
                WHERE w.slug = :wsslug AND p.name =:protocol AND t.token =:token ';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('token', $token);
        $consulta->setParameter('wsslug', $wsslug);
        $consulta->setParameter('protocol', $protocol);

        return $consulta->getResult();
    }
    
       public function findWebServiceUserPass($wsslug, $protocol, $user) {
        $em = $this->getEntityManager();
        $dql = 'SELECT w,p,u
                FROM WebServiceBundle:WebService w
                JOIN w.protocol p
                JOIN w.user u
                WHERE w.slug = :wsslug AND p.name =:protocol AND u.username =:user';

        $consulta = $em->createQuery($dql);
        $consulta->setParameter('user', $user);
     
        $consulta->setParameter('wsslug', $wsslug);
        $consulta->setParameter('protocol', $protocol);

        return $consulta->getResult();
    }

}

?>
