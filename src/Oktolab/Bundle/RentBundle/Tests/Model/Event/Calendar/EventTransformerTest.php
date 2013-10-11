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

    /**
     * This test generates a lot of Test-Fixture in_memory to assert the TestCase.
     */
    public function testGetFormattedActiveEventsReturnsFormattedArray()
    {
        $date = new \DateTime();
        $expected = array(
            '2' => array(
                'id'            => 2,
                'title'         => 'New Ordner',
                'name'          => 'New Ordner',
                'begin'         => $date->modify('2013-10-09 08:00')->format('c'),
                'end'           => $date->modify('2013-10-12 17:00')->format('c'),
                'uri'           => '/event/2/edit',
                'description'   => 'This is a event for new ordner',
                'state'         => 'RESERVED',
                'begin_view'    => 'Mi, 09.10. 08:00',
                'end_view'      => 'Sa, 12.10. 17:00',
                'objects'       => array(
                    array('object_id' => 'item:3', 'title' => 'LIKO4', 'uri' => '/inventory/item/3'),
                    array('object_id' => 'item:5', 'title' => 'JVC123', 'uri' => '/inventory/item/5'),
                ),
            ),
        );

        $eventObject1 = new EventObject();
        $eventObject1->setType('item')->setObject('3'); // LIKO4
        $eventObject2 = new EventObject();
        $eventObject2->setType('item')->setObject('5'); // JVC123

        $event = $this->getMock('\Oktolab\Bundle\RentBundle\Entity\Event', array('getId'));
        $event->expects($this->any())->method('getId')->will($this->returnValue(2));
        $event->setName('New Ordner')
            ->setBegin(clone $date->modify('2013-10-09 08:00'))
            ->setEnd(clone $date->modify('2013-10-12 17:00'))
            ->setDescription('This is a event for new ordner')
            ->setState(Event::STATE_RESERVED)
            ->addObject($eventObject1)
            ->addObject($eventObject2);

        $item1 = $this->getMock('\Oktolab\Bundle\RentBundle\Entity\Inventory\Item', array('getTitle', 'getId'));
        $item1->expects($this->any())->method('getTitle')->will($this->returnValue('LIKO4'));
        $item1->expects($this->any())->method('getId')->will($this->returnValue(3));

        $item2 = $this->getMock('\Oktolab\Bundle\RentBundle\Entity\Inventory\Item', array('getTitle', 'getId'));
        $item2->expects($this->any())->method('getTitle')->will($this->returnValue('JVC123'));
        $item2->expects($this->any())->method('getId')->will($this->returnValue('5'));

        $this->aggregator->expects($this->once())->method('getActiveEvents')->will($this->returnValue(array($event)));

        $this->eventManager->expects($this->once())
            ->method('convertEventObjectsToEntites')
            ->will($this->returnValue(array($item1, $item2)));

        $this->router->expects($this->at(0))
            ->method('generate')
            ->with($this->equalTo('OktolabRentBundle_Event_Edit'), $this->equalTo(array('id' => 2)))
            ->will($this->returnValue('/event/2/edit'));

        $this->router->expects($this->at(1))
            ->method('generate')
            ->with($this->equalTo('inventory_item_show'), $this->equalTo(array('id' => 3)))
            ->will($this->returnValue('/inventory/item/3'));

        $this->router->expects($this->at(2))
            ->method('generate')
            ->with($this->equalTo('inventory_item_show'), $this->equalTo(array('id' => 5)))
            ->will($this->returnValue('/inventory/item/5'));

        $events = $this->SUT->getFormattedActiveEvents(clone $date->modify('2013-10-31'), 'inventory');
        $this->assertEquals($expected, $events, 'Expected formatted Events-Array');
    }
}
