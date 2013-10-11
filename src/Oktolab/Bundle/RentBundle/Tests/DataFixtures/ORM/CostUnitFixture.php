<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;

/**
 * Description of CostUnitFixture
 *
 * @author rs
 */
class CostUnitFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $costunit = new CostUnit();
        $costunit->setName('Dummy Costunit');
        $costunit->setGuid('1234567DUMMY');

        $manager->persist($costunit);
        $manager->flush();
    }
}
