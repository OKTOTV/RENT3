<?php

namespace Oktolab\Bundle\RentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;

class ItemFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager, $number = 1)
    {
     for ($index = 0; $index < $number; $index++) {
         $item = new Item();
         $item->setTitle('ItemTitle'.$index);
         $item->setDescription('ItemDescription'.$index);
         $item->setBarcode('ITEM'.$index);

         $manager->persist($item);
     }

     $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }


}

?>
