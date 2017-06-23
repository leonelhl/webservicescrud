<?php

namespace Xalix\WebServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Xalix\WebServiceBundle\Entity\Protocol;
use Xalix\WebServiceBundle\Util\UpdateRest;

class ProtocolFixtures extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder() {

        return 1;
    }

    public function load(ObjectManager $manager) {
        $protocols = array(
            array('nombre' => 'SOAP', 'description' => 'Simple Object Access Protocol', 'isActive' => true,),
            array('nombre' => 'REST', 'description' => 'REpresentational State Transfer', 'isActive' => true,),
        );

        foreach ($protocols as $protocol) {
            $entity = new Protocol();
            $entity->setName($protocol['nombre']);
            $entity->setDescription($protocol['description']);
            $entity->setIsActive($protocol['isActive']);
            $manager->persist($entity);
        }
        $manager->flush();
        $rest = new UpdateRest();
        /*
         * Se le pasa la cadena 'nathing' porque no tiene que eliminar ningún archivo
         * de rutas.
         */
        $rest->updatePublishRestServices($manager, 'nathing');
    }

}

?>
