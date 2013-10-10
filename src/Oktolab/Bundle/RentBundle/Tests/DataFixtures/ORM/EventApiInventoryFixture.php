<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Oktolab\Bundle\RentBundle\Entity\Inventory\Category;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;

/**
 * EventApi Inventory
 */
class EventApiInventoryFixture extends AbstractFixture
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $place = new Place();  // dependency
        $place->setTitle('Okto Verleih');

        $itemA = new Item();
        $itemA->setTitle('Camera JVC 500')
            ->setBarcode('A5DF01')
            ->setDescription('Item "Camera JVC 500" used for test purposes.')
            ->setPlace($place);

        $itemB = new Item();
        $itemB->setTitle('Camera Blackmagic 123')
            ->setBarcode('B6EG12')
            ->setDescription('Item "Camera Blackmagic 123" used for test purposes.')
            ->setPlace($place);

        $category = new Category();
        $category->setTitle('Camera')
            ->addItem($itemA)
            ->addItem($itemB);

        $itemA->setCategory($category);
        $itemB->setCategory($category);

        $manager->persist($place);
        $manager->persist($category);
        $manager->persist($itemA);
        $manager->persist($itemB);
        $manager->flush();
    }
}
