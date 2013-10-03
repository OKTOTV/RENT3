<?php

namespace Oktolab\Bundle\RentBundle\Test\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Entity\Timeblock;
use Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockTransformer;
use Doctrine\Common\Cache\ArrayCache;

/**
 * Description of TimeblockTransformerTest
 *
 * @author meh
 */
class TimeblockTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System Under Test
     *
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockTransformer
     */
    protected $SUT = null;

    /**
     * @var \PHPUnit_Framwork_MockBuilder|\Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockTransformer
     */
    protected $aggregator = null;

    public function setUp()
    {
        parent::setUp();

        $this->aggregator = $this->getMock('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator');
        $this->SUT = new TimeblockTransformer($this->aggregator, new ArrayCache());
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockTransformer', $this->SUT);
    }

    public function testGetTransformedTimeblocksThrowsExceptionOnInvalidEndDate()
    {
        $this->setExpectedException('\LogicException');
        $this->SUT->getTransformedTimeblocks(new \DateTime('now'), new \DateTime('-3 hours'));
    }

    public function testGetTransformedTimeblocksReturnsFormattedArray()
    {
        $this->markTestIncomplete();
        $date = new \DateTime('2013-10-02');
        $expected = array(
            array(
                'date'  => $date->format('c'),
                'begin' => $date->modify('2013-10-02 12:00')->format('c'),
                'end'   => $date->modify('2013-10-02 17:00')->format('c')
            ),
        );

        $timeblock = new Timeblock();
        $timeblock->setWeekdaysAsArray(array(Timeblock::WEEKDAY_TH))
            ->setIntervalBegin(new \DateTime('2013-10-02'))
            ->setIntervalEnd(new \DateTime('2013-10-02'))
            ->setBegin(new \DateTime('2013-10-02 12:00'))
            ->setEnd(new \DateTime('2013-10-02 17:00'));

        $this->aggregator->expects($this->once())->method('getTimeblocks')->will($this->returnValue(array($timeblock)));
        $this->assertEquals($expected, $this->SUT->getTransformedTimeblocks());
    }

    public function testSeparateTimeblocksForOneDay()
    {
        $timeblock = new Timeblock();
        $timeblock->setWeekdaysAsArray(array(Timeblock::WEEKDAY_TH))
            ->setIntervalBegin(new \DateTime('2013-10-03'))
            ->setIntervalEnd(new \DateTime('2013-10-03'))
            ->setBegin(new \DateTime('2013-10-03 12:00'))
            ->setEnd(new \DateTime('2013-10-03 17:00'));

        $date = new \DateTime('2013-10-03');
        $expected = array(
            array(
                'date'      => $date,
                'begin'     => $timeblock->getBegin(),
                'end'       => $timeblock->getEnd(),
                'timeblock' => $timeblock,
            ),
        );

        $this->aggregator->expects($this->once())
            ->method('getTimeblocks')
            ->will($this->returnValue(array($timeblock)));

        $this->assertEquals(
            $expected,
            $this->SUT->getSeparatedTimeblocks(new \DateTime('2013-10-03'), new \DateTime('2013-10-03'))
        );
    }

    public function testSeparateTimeblocksForSevenDays()
    {
        $timeblock = new TimeBlock();
        $timeblock->setWeekdays(1016)
            ->setIntervalBegin(new \DateTime('2013-01-01'))
            ->setIntervalEnd(new \DateTime('2013-12-31'))
            ->setBegin(new \DateTime('2013-01-01 12:00'))
            ->setEnd(new \DateTime('2013-12-31 17:00'));

        $this->aggregator->expects($this->once())
            ->method('getTimeblocks')
            ->will($this->returnValue(array($timeblock)));

        $this->assertCount(
            7,
            $timeblocks = $this->SUT->getSeparatedTimeblocks(new \DateTime('2013-09-29'), new \DateTime('2013-10-05'))
        );

        $intervalDate = new \DateTime('2013-09-29');
        foreach ($timeblocks as $timeblock) {
            $this->assertEquals($intervalDate, $timeblock['date']);
            $intervalDate->modify('+1 day');
        }
    }

    public function testSeparateTimeblocksForTwoWeekdays()
    {
        $timeblock = new TimeBlock();
        $timeblock->setWeekdays(40)
            ->setIntervalBegin(new \DateTime('2013-01-01'))
            ->setIntervalEnd(new \DateTime('2013-12-31'))
            ->setBegin(new \DateTime('2013-01-01 12:00'))
            ->setEnd(new \DateTime('2013-12-31 17:00'));

        $this->aggregator->expects($this->once())
            ->method('getTimeblocks')
            ->will($this->returnValue(array($timeblock)));

        $this->assertCount(
            2,
            $timeblocks = $this->SUT->getSeparatedTimeblocks(new \DateTime('2013-09-29'), new \DateTime('2013-10-05'))
        );

        $this->assertEquals(new \DateTime('2013-09-30'), $timeblocks[0]['date']);
        $this->assertEquals(new \DateTime('2013-10-02'), $timeblocks[1]['date']);
    }

    public function testSeparateTimeblocksMergesToTimeblocks()
    {
        $this->markTestIncomplete();
    }

    public function testSeparateTimeblocksReturnsMaximumNumberItems()
    {
        $this->markTestIncomplete();
    }
}
