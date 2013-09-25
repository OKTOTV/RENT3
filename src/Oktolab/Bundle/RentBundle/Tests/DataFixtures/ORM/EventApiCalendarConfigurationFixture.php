<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Category;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;

class EventApiCalendarConfigurationFixture extends AbstractFixture
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $place = new Place();
        $place->setTitle('Test Place');
        $manager->persist($place);

        $item = new Item();
        $item->setTitle('Camera JVC 500')
            ->setBarcode('A5DF01')
            ->setDescription('Item used for test purposes.')
            ->setPlace($place);

        $category = new Category();
        $category->setTitle('Camera')
            ->addItem($item);

        $item->setCategory($category);

        $manager->persist($category);
        $manager->persist($item);
        $manager->flush();
    }
}
