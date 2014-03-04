<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\API;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Category;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author rs
 */
class ItemApiFixture extends AbstractFixture
{
    public function load(ObjectManager $manager) {
        $place = new Place();
        $place->setTitle('Testplace');
        $manager->persist($place);

        $category = new Category();
        $category->setTitle('Testcategory');

        $item1 = new Item();
        $item1->setTitle('Testitem1');
        $item1->setDescription('Testitem1 description');
        $item1->setBarcode('APITEST1');
        $item1->setCategory($category);
        $item1->setPlace($place);

        $category->addItem($item1);
        $manager->persist($category);

        $manager->persist($item1);
        $manager->flush();
    }
}
