<?php

namespace Oktolab\Bundle\RentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;

class SetFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager, $number = 1)
    {
        for ($index = 0; $index < $number; $index++) {
            $set = new Set();
            $set->setTitle('SetTitle'.$index);
            $set->setDescription('SetDescription'.$index);
            $set->setBarcode('ASDF'.$index);

            $manager->persist($set);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }

    public function setWithItem(ObjectManager $manager)
    {
        $set = new Set();
        $set->setTitle('SetWithItemTitle');
        $set->setDescription('SetWithItemDescription');
        $set->setBarcode('ASDF0');

        $manager->persist($set);

        $item = new Item();
        $item->setSet($set);
        $item->setTitle('ItemForSetTitle');
        $item->setDescription('ItemForSetDescription');
        $item->setBarcode('YODAWG');

        $manager->persist($item);

        $manager->flush();
    }
}
