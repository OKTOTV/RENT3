<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class EventManagerFunctionalTest extends WebTestCase
{

    public function testItemIsAvailable()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\EventManagerFixture'));
        $item = static::$kernel->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('OktolabRentBundle:Inventory\Item')
            ->findOneByTitle('eventItem');

        $em = static::$kernel->getContainer()->get('oktolab.event_manager');
        $this->assertTrue($em->isAvailable($item, new \DateTime('14:00'), new \DateTime('14:30')), '14:00 - 14:30');
        $this->assertFalse($em->isAvailable($item, new \DateTime('11:00'), new \DateTime('17:00')), '11:00 - 17:00');
        $this->assertFalse($em->isAvailable($item, new \DateTime('12:30'), new \DateTime('14:00')), '12:30 - 14:00');
        $this->assertFalse($em->isAvailable($item, new \DateTime('12:00'), new \DateTime('13:00')), '12:00 - 13:00');
        $this->assertFalse($em->isAvailable($item, new \DateTime('14:00'), new \DateTime('15:30')), '14:00 - 15:30');
    }

    public function testRentAnItem()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture'));
        $item = static::$kernel->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('OktolabRentBundle:Inventory\Item')
            ->findOneById(1);

        $em = static::$kernel->getContainer()->get('oktolab.event_manager');
        $event = $em->rent(array($item), new \DateTime('15:00'), new \DateTime('17:00'));

        $this->assertEquals(new \DateTime('15:00'), $event->getBegin());
        $this->assertEquals(new \DateTime('17:00'), $event->getEnd());
        $this->assertTrue($event->isRented());
        $this->assertFalse($em->isAvailable($item, new \DateTime('15:00'), new \DateTime('17:00')));
    }

    public function testRentWithOutItems()
    {
        $this->loadFixtures(array());
        $em = static::$kernel->getContainer()->get('oktolab.event_manager');
        $entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        try {
            $event = $em->rent(array(), new \DateTime('15:00'), new \DateTime('17:00'));
        } catch (\BadMethodCallException $e) {
            $events = $entityManager->getRepository('OktolabRentBundle:Event')->findAll();
            $this->assertSame(0, count($events));
            $this->assertSame('Expected array with RentableInterface objects, empty array given', $e->getMessage());

            return;
        }

        $this->fail('EventManager should not allow rent empty array of objects.');
    }
}
