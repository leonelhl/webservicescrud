<?php

namespace Xalix\WebServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Xalix\WebServiceBundle\Entity\Type;

class TypeFixtures extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder() {

        return 3;
    }

    public function load(ObjectManager $manager) {
        $types = array(
            array('type' => 'string'),
            array('type' => 'boolean'),
            array('type' => 'decimal'),
            array('type' => 'float'),
            array('type' => 'double'),
            array('type' => 'duration'),
            array('type' => 'dateTime'),
            array('type' => 'time'),
            array('type' => 'date'),
            array('type' => 'binary'),
            array('type' => 'gYearMonth'),
            array('type' => 'gYear'),
            array('type' => 'gMonthDay'),
            array('type' => 'gDay'),
            array('type' => 'gMonth'),
            array('type' => 'hexBinary'),
            array('type' => 'base64Binary'),
            array('type' => 'anyURI'),
            array('type' => 'QName'),
            array('type' => 'NOTATION'),
            array('type' => 'normalizedString'),
            array('type' => 'token'),
            array('type' => 'language'),
            array('type' => 'NMTOKEN'),
            array('type' => 'NMTOKENS'),
            array('type' => 'Name'),
            array('type' => 'NCName'),
            array('type' => 'ID'),
            array('type' => 'IDREF'),
            array('type' => 'IDREFS'),
            array('type' => 'ENTITY'),
            array('type' => 'ENTITIES'),
            array('type' => 'integer'),
            array('type' => 'int'),
            array('type' => 'long'),
            array('type' => 'number'),
            array('type' => 'nonPositiveInteger'),
            array('type' => 'negativeInteger'),
            array('type' => 'short'),
            array('type' => 'byte'),
            array('type' => 'nonNegativeInteger'),
            array('type' => 'unsignedLong'),
            array('type' => 'unsignedInt'),
            array('type' => 'unsignedShort'),
            array('type' => 'unsignedByte'),
            array('type' => 'positiveInteger'),
            array('type' => 'anyType'),
            array('type' => 'anyXML'),
            array('type' => 'anyJSON'),
            array('type' => 'anySimpleType'),
            array('type' => 'struct'),
            array('type' => 'array'),
            array('type' => 'void'),
            array('type' => 'UNKNOWN_TYPE'),
        );

        foreach ($types as $type) {
            $entity = new Type();
            $entity->setType($type['type']);
            $entity->setIsComplexType(false);
            $entity->setIsArray(false);
            $manager->persist($entity);
        }
        
        foreach ($types as $array) {
            if (!($array['type'] == 'void' || $array['type'] == 'array')) {
                $entity = new Type();
                $entity->setType($array['type'] . '[]');
                $entity->setIsComplexType(false);
                $entity->setIsArray(true);
                $manager->persist($entity);
            }
        }

        $manager->flush();
    }

}

?>
