<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\EventTransformer;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Doctrine\Common\Cache\ArrayCache;

/**
 * Description of EventTransformerTest
 *
 * @author meh
 */
class EventTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\EventTransformer
     */
    protected $SUT = null;

    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\EventAggregator
     */
    protected $aggregator = null;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache = null;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router = null;

    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\EventManager
     */
    protected $eventManager = null;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->aggregator   = $this->getMock('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\EventAggregator');
        $this->eventManager = $this->getMock('\Oktolab\Bundle\RentBundle\Model\Event\EventManager');
        $this->cache        = new ArrayCache();
        $this->router       = $this->getMockBuilder('\Symfony\Bundle\FrameworkBundle\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();

        $this->SUT = new EventTransformer($this->aggregator, $this->router, $this->eventManager, $this->cache);
        $this->assertInstanceOf(
            '\Oktolab\Bundle\RentBundle\Model\Event\Calendar\EventTransformer',
            $this->SUT,
            'Should be instance of EventTransformerTest'
        );
    }

    public function testGetFormattedActiveEventsThrowsExceptionOnPastDate()
    {
        $this->setExpectedException('\LogicException');
        $this->SUT->getFormattedActiveEvents(new \DateTime('-1 day'));
    }

    public function testGetFormattedActiveEventsReturnsFormattedArray()
    {
        $this->markTestIncomplete('WIP');
        $date = new \DateTime();
        $expected = array(
            '2' => array(
                'id'            => 2,
                'title'         => '08:<sup>00</sup> New Ordner',
                'name'          => 'New Ordner',
                'begin'         => $date->modify('2013-10-09 08:00')->format('c'),
                'end'           => $date->modify('2013-10-12 17:00')->format('c'),
                'uri'           => '/event/2/edit',
                'description'   => 'This is a event for new ordner',
                'state'         => 'STATE_RESERVED',
                'objectives'    => array(
                    array('objective' => 'item:3', 'title' => 'LIKO4', 'uri' => '/inventory/item/3'),
                    array('objective' => 'item:5', 'title' => 'JVC123', 'uri' => '/inventory/item/5'),
                ),
            ),
        );

        $eventObject1 = new EventObject();
        $eventObject1->setType('item')->setObject('3'); // LIKO4
        $eventObject2 = new EventObject();
        $eventObject2->setType('item')->setObject('5'); // JVC123

        $event = $this->getMock('\Oktolab\Bundle\RentBundle\Entity\Event', array('getId'));
        $event->expects($this->once())->method('getId')->will($this->returnValue(2));
        $event->setName('New Ordner')
            ->setBegin($date->modify('2013-10-09 08:00'))
            ->setEnd($date->modify('2013-10-12 17:00'))
            ->setDescription('This is a event for new ordner')
            ->setState(Event::STATE_RESERVED)
            ->addObject($eventObject1)
            ->addObject($eventObject2);

        $this->router->expects($this->once())
            ->method('generate')
            ->with($this->equalTo('OktolabRentBundle_Event_Edit'), $this->equalTo(array('id' => 2)))
            ->will($this->returnValue('/event/2/edit'));

        $this->aggregator->expects($this->once())
            ->method('getActiveEvents')
            ->will($this->returnValue(array($event)));

        $this->eventManager->expects($this->once())
            ->method('convertEventObjectsToEntites')
            ->with($this->equalTo(array($eventObject1, $eventObject2)))
            ->will($this->returnValue(/* Item1, Item2 .. */));

        // router expects Item1 && Item2 routes

        $events = $this->SUT->getFormattedActiveEvents(clone $date->modify('2013-10-31'), 'inventory');
        $this->assertEquals($expected, $events, 'Expected formatted Events-Array');
    }
}
