<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Model\SeriesEvent\SeriesEventService;
use Oktolab\Bundle\RentBundle\Entity\SeriesEvent;
use Oktolab\Bundle\RentBundle\Entity\EventType;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;
use Oktolab\Bundle\RentBundle\Entity\EventObject;

/**
 * @author rs
 * SeriesEventServiceTest
 */
class SeriesEventServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testPrepareSeriesEventInventory()
    {
        $repositoryMock = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(array('name' => 'inventory'))
            ->will($this->returnValue(new EventType()));

        $managerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $managerMock->expects($this->once())
            ->method('getRepository')
            ->with('OktolabRentBundle:EventType')
            ->will($this->returnValue($repositoryMock));

        $SUT = new SeriesEventService($managerMock);

        $costunit = new CostUnit();
        $costunit->setName('mock testunit');

        $event_object = new EventObject();

        $series_event = new SeriesEvent();
        $series_event->setEventBegin(new \DateTime('2014-01-01 10:00:00'));
        $series_event->setEventEnd(new \DateTime('2014-01-01 20:00:00'));
        $series_event->setEnd(new \DateTime('2014-01-05 10:00:01'));
        $series_event->setRepetition(1);
        $series_event->setCostUnit($costunit);
        $series_event->addObject($event_object);

        $prepared_series = $SUT->prepareSeriesEvent($series_event);

        $allEvents = $prepared_series->getEvents();
        $this->assertEquals(5, count($allEvents));
        $this->assertEquals('2014-01-01 10:00:00', $allEvents[0]->getBegin()->format('Y-m-d H:i:s'));
        $this->assertEquals('2014-01-01 20:00:00', $allEvents[0]->getEnd()->format('Y-m-d H:i:s'));

        $this->assertEquals('2014-01-02 10:00:00', $allEvents[1]->getBegin()->format('Y-m-d H:i:s'));
        $this->assertEquals('2014-01-02 20:00:00', $allEvents[1]->getEnd()->format('Y-m-d H:i:s'));

        $this->assertEquals('2014-01-03 10:00:00', $allEvents[2]->getBegin()->format('Y-m-d H:i:s'));
        $this->assertEquals('2014-01-03 20:00:00', $allEvents[2]->getEnd()->format('Y-m-d H:i:s'));

        $this->assertEquals('2014-01-04 10:00:00', $allEvents[3]->getBegin()->format('Y-m-d H:i:s'));
        $this->assertEquals('2014-01-04 20:00:00', $allEvents[3]->getEnd()->format('Y-m-d H:i:s'));

        $this->assertEquals('2014-01-05 10:00:00', $allEvents[4]->getBegin()->format('Y-m-d H:i:s'));
        $this->assertEquals('2014-01-05 20:00:00', $allEvents[4]->getEnd()->format('Y-m-d H:i:s'));
    }
}
