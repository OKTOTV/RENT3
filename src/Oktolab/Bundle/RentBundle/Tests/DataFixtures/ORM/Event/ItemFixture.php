<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;

/**
 *  Loads a fixture Item.
 */
class ItemFixture extends AbstractFixture
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $place = new Place();
        $place->setTitle('Okto Verleih');

        $item1 = new Item();
        $item1->setTitle('JVC Camera 123')
            ->setDescription('A JVC Camera.')
            ->setBarcode('F00B5R')
            ->setPlace($place);

        $item2 = new Item();
        $item2->setTitle('Blackmagic Camera 456')
            ->setDescription('Blackmagic Camera.')
            ->setBarcode('B5ZF00')
            ->setPlace($place);

        $manager->persist($place);
        $manager->persist($item1);
        $manager->persist($item2);
        $manager->flush();
    }
}
