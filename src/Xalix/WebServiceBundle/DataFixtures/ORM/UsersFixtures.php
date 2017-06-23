<?php

namespace Xalix\WebServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Xalix\WebServiceBundle\Entity\Users;

class UsersFixtures extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder() {

        return 5;
    }

    public function load(ObjectManager $manager) {
        for ($i = 0; $i < 10; $i++) {
            $entity = new Users();
            $entity->setUsername('leox'.$i);
            $entity->setPassword('leox'.$i);
            $manager->persist($entity);
        }
        $manager->flush();
    }

}

?>
