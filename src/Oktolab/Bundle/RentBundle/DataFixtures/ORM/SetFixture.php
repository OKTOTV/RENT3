<?php

namespace Oktolab\Bundle\RentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;

class SetFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager, $number = 1)
    {
     for ($index = 0; $index < $number; $index++) {
         $set = new Set();
         $set->setTitle('SetTitle'.$index);
         $set->setDescription('SetDescription'.$index);

         $manager->persist($set);
     }

     $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }

}

?>