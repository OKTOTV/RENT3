<?php

namespace Oktolab\Bundle\RentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;

/**
 *  Loads a fixture Set with attached Item.
 */
class SetWithItemFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $item = new Item();
        $item->setTitle('SharedItem');
        $item->setDescription('Item Description');
        $item->setBarcode('METI123');

        $manager->persist($item);

        $set = new Set();
        $set
            ->setTitle('SetWithItemTitle')
            ->setDescription('SetWithItemDescription')
            ->setBarcode('ASDF0');

        $set->addItem($item);
        $manager->persist($set);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
