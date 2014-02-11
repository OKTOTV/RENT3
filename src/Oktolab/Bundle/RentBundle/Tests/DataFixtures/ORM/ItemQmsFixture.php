<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Qms;

/**
 *  Loads a fixture Item.
 */
class ItemQmsFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $place = new Place();
        $place->setTitle('Test Place');

        $item = new Item();
        $item->setTitle('With Qms History')
             ->setDescription('An item with a Qms History.')
             ->setBarcode('QMS001')
             ->setPlace($place)
             ->setBuyDate(new \DateTime('1991-10-27 12:00:00'));

        $qms = new Qms();
        $qms->setItem($item);
        $qms->setStatus(Qms::STATE_OKAY);
        $qms->setDescription('random description');

        $item->addQms($qms);

        $manager->persist($place);
        $manager->persist($item);
        $manager->persist($qms);

        $manager->flush();
    }
}
