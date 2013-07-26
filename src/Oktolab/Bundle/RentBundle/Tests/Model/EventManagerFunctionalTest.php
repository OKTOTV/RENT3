<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class EventManagerFunctionalTest extends WebTestCase
{

    public function testItemIsAvailable()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\EventManagerFixture'));
        $entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $em = static::$kernel->getContainer()->get('oktolab.event_manager');

        $item = $entityManager->getRepository('OktolabRentBundle:Inventory\Item')->findOneByTitle('eventItem');

        $this->assertTrue($em->isAvailable($item, new \DateTime('14:00'), new \DateTime('14:30')), '14:00 - 14:30');
        $this->assertFalse($em->isAvailable($item, new \DateTime('11:00'), new \DateTime('17:00')), '11:00 - 17:00');
        $this->assertFalse($em->isAvailable($item, new \DateTime('12:30'), new \DateTime('14:00')), '12:30 - 14:00');
        $this->assertFalse($em->isAvailable($item, new \DateTime('12:00'), new \DateTime('13:00')), '12:00 - 13:00');
        $this->assertFalse($em->isAvailable($item, new \DateTime('14:00'), new \DateTime('15:30')), '14:00 - 15:30');
    }
}
