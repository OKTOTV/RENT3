<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\API;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;

/**
 * Description of CostUnitApiFixture
 *
 * @author rs
 */
class CostUnitApiFixture extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $costunit = new CostUnit();
        $costunit->setName('Test costunit');
        $costunit->setAbbreviation('TC');
        $costunit->setGuid('testcostunit01');
        $manager->persist($costunit);
        $manager->flush();
    }
}
