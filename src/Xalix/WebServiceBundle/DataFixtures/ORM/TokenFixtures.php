<?php

namespace Xalix\WebServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Xalix\WebServiceBundle\Entity\Token;

class TokenFixtures extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder() {

        return 4;
    }

    public function load(ObjectManager $manager) {
        for ($i = 0; $i < 10; $i++) {
            $entity = new Token();
            $entity->setToken(md5(microtime() . 'leox'));
            $manager->persist($entity);
        }
        $manager->flush();
    }

}

?>
