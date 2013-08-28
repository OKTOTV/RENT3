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
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\EventManagerFixture'));

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

        try {
            $em->rent(array(), new \DateTime('15:00'), new \DateTime('17:00'));
        } catch (\BadMethodCallException $e) {
            $entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
            $events = $entityManager->getRepository('OktolabRentBundle:Event')->findAll();

            $this->assertSame(0, count($events));
            $this->assertSame('Expected array with RentableInterface objects, empty array given', $e->getMessage());

            return;
        }

        $this->fail('EventManager should not allow rent empty array of objects.');
    }
}
