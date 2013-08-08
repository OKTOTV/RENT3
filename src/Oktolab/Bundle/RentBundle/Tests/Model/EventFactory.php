<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Model\EventFactory;
use Oktolab\Bundle\RentBundle\Model\EventManager;

class EventManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateReturnsNewEvent()
    {
        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $eventManager = new EventManager($entityManager);
        $eventFactory = new EventFactory($eventManager);

        $event = $eventFactory->create();

        $this->assertEquals(new Event(), $event);
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\Event', $event);
    }
}
