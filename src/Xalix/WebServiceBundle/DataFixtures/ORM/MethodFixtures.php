<?php

namespace Xalix\WebServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Xalix\WebServiceBundle\Entity\Method;

class MethodFixtures extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder() {
        
        return 2;
    }

    public function load(ObjectManager $manager) {
        $methods = array(
            //Metodos de transporte para REST
            array('nombre' => 'POST'),
            array('nombre' => 'GET'),
            array('nombre' => 'PUT'),
            array('nombre' => 'DELETE'),
            //Combinaciones POST para dar servicio por REST
//            array('nombre' => 'POST|GET'),
//            array('nombre' => 'POST|PUT'),
//            array('nombre' => 'POST|DELETE'),
//            array('nombre' => 'POST|GET|PUT'),
//            array('nombre' => 'POST|GET|DELETE'),
//            array('nombre' => 'POST|PUT|DELETE'),
//            array('nombre' => 'POST|GET|PUT|DELETE'),
//            //Combinaciones GET
//            array('nombre' => 'GET|PUT'),
//            array('nombre' => 'GET|DELETE'),
//            array('nombre' => 'GET|PUT|DELETE'),
//            //Combinaciones PUT
//            array('nombre' => 'PUT|DELETE'),
        );
        $REST = $manager->getRepository('WebServiceBundle:Protocol')->findOneBy(array(
            'name' => 'REST'
        ));

        foreach ($methods as $method) {
            $entity = new Method();
            $entity->setName($method['nombre']);
            $entity->setProtocol($REST);
            $manager->persist($entity);
        }

        $manager->flush();
    }

}

?>
