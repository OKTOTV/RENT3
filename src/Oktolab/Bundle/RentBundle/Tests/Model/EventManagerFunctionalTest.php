<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class EventManagerFunctionalTest extends WebTestCase
{

    /**
     * @dataProvider itemIsAvailableProvider
     */
    public function testItemIsAvailable($begin, $end, $comment, $assertion)
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\EventManagerFixture'));

        $em = static::$kernel->getContainer()->get('oktolab.event_manager');
        $item = static::$kernel->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('OktolabRentBundle:Inventory\Item')
            ->findOneBy(array('id' => 1));

        $this->assertSame($em->isAvailable($item, $begin, $end), $assertion, $comment);
    }

    public function itemIsAvailableProvider()
    {
        return array(
            array(new \DateTime('2013-08-28 14:00'), new \DateTime('2013-08-28 14:30'), '14:00 - 14:30', true),
            array(new \DateTime('2013-08-28 11:00'), new \DateTime('2013-08-28 17:00'), '11:00 - 17:00', false),
            array(new \DateTime('2013-08-28 12:30'), new \DateTime('2013-08-28 14:00'), '12:30 - 14:00', false),
            array(new \DateTime('2013-08-28 12:00'), new \DateTime('2013-08-28 13:00'), '12:00 - 13:00', false),
            array(new \DateTime('2013-08-28 14:00'), new \DateTime('2013-08-28 15:30'), '14:00 - 15:30', false),
        );
    }

    public function testRentAnItem()
    {
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ItemFixture',
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'));
        $eventType = static::$kernel->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('OktolabRentBundle:EventType')
            ->findOneBy(array('name' => 'inventory'));

        $item = static::$kernel->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('OktolabRentBundle:Inventory\Item')
            ->findOneById(1);

        $em = static::$kernel->getContainer()->get('oktolab.event_manager');
        $event = $em->create(array($item));

        $event
            ->setBegin(new \DateTime('2013-08-28 15:00'))
            ->setEnd(new \DateTime('2013-08-28 17:00'))
            ->setName('Test Event')
            ->setType($eventType);

        $rentedEvent = $em->rent($event);
        $em->save($event);

        $this->assertEquals(new \DateTime('2013-08-28 15:00'), $rentedEvent->getBegin());
        $this->assertEquals(new \DateTime('2013-08-28 17:00'), $rentedEvent->getEnd());
        $this->assertTrue($rentedEvent->isRented());
        $this->assertFalse($em->isAvailable($item, new \DateTime('2013-08-28 15:00'), new \DateTime('2013-08-28 17:00')));
    }
}
