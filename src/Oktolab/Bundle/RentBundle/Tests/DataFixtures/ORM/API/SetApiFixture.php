<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\API;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Category;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author rs
 */
class SetApiFixture extends AbstractFixture
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
        $item1->setBarcode('APITESTITEM1');
        $item1->setCategory($category);
        $item1->setPlace($place);

        $category->addItem($item1);
        $manager->persist($category);


        $set1 = new Set();
        $set1->setTitle('Testset1');
        $set1->setBarcode('APITESTSET1');
        $set1->setDescription('Testset description');
        $set1->setPlace($place);
        $set1->addItem($item1);

        $item1->setSet($set1);
        $manager->persist($set1);
        $manager->persist($item1);
        $manager->flush();
    }
}
