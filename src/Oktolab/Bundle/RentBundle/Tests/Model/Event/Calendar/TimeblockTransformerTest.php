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
        $this->markTestIncomplete('Test will be written after getSeparatedTimeblocks works as expected.');
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

        $this->aggregator->expects($this->once())
            ->method('getTimeblocks')
            ->will($this->returnValue(array($timeblock)));

        $this->assertEquals($expected, $this->SUT->getTransformedTimeblocks());
    }

    public function testSeparateTimeblocksThrowsExceptionOnInvalidEndDate()
    {
        $this->setExpectedException('\LogicException');
        $this->SUT->getSeparatedTimeblocks(new \DateTime('now'), new \DateTime('-3 hours'));
    }

    public function testSeparateTimeblocksForOneDay()
    {
        $timeblock = $this->trainADefaultTimeblock()
            ->setWeekdaysAsArray(array(Timeblock::WEEKDAY_TH));

        $expected = array(
            array(
                'date'      => new \DateTime('2013-10-03'),
                'begin'     => new \DateTime('2013-10-03 12:00'),
                'end'       => new \DateTime('2013-10-03 17:00'),
                'timeblock' => $timeblock,
            ),
        );

        $this->trainTheAggregatorToReturn(array($timeblock));
        $timeblocks = $this->SUT->getSeparatedTimeblocks(new \DateTime('2013-10-03'), new \DateTime('2013-10-03'));
        $this->assertEquals($expected, $timeblocks);
    }

    public function testSeparateTimeblocksForSevenDays()
    {
        $timeblock = $this->trainADefaultTimeblock();
        $this->trainTheAggregatorToReturn(array($timeblock));

        $timeblocks = $this->SUT->getSeparatedTimeblocks(new \DateTime('2013-09-29'), new \DateTime('2013-10-05'));
        $this->assertCount(7, $timeblocks);
    }

    public function testSeparateTimeblocksForTwoWeekdays()
    {
        $timeblock = $this->trainADefaultTimeblock()
            ->setWeekdaysAsArray(array(Timeblock::WEEKDAY_MO, Timeblock::WEEKDAY_WE));

        $this->trainTheAggregatorToReturn(array($timeblock));
        $timeblocks = $this->SUT->getSeparatedTimeblocks(new \DateTime('2013-09-29'), new \DateTime('2013-10-05'));

        $this->assertCount(2, $timeblocks);
        $this->assertEquals(new \DateTime('2013-09-30'), $timeblocks[0]['date']);
        $this->assertEquals(new \DateTime('2013-10-02'), $timeblocks[1]['date']);
    }

    public function testSeparateTimeblocksMergesTwoTimeblocks()
    {
        $timeblockA = $this->trainADefaultTimeblock();
        $timeblockB = $this->trainADefaultTimeblock()
            ->setBegin(new \DateTime('2013-01-01 08:00'))
            ->setEnd(new \DateTime('2013-12-31 11:00'));

        $this->trainTheAggregatorToReturn(array($timeblockA, $timeblockB));
        $timeblocks = $this->SUT->getSeparatedTimeblocks(new \DateTime('2013-10-02'), new \DateTime('2013-10-04'));

        $this->assertCount(6, $timeblocks);
    }

    public function testSeparateTimeblocksReturnsMaximumNumberItems()
    {
        $timeblock = $this->trainADefaultTimeblock();
        $this->trainTheAggregatorToReturn(array($timeblock));

        $timeblocks = $this->SUT->getSeparatedTimeblocks(new \DateTime('2013-01-01'), new \DateTime('2013-04-01'), 30);
        $this->assertCount(30, $timeblocks);
    }

    public function testSeparateTimeblocksSortsDatesByBeginDate()
    {
        $timeblockA = $this->trainADefaultTimeblock();
        $timeblockB = $this->trainADefaultTimeblock()
            ->setBegin(new \DateTime('2013-01-01 08:00'))
            ->setEnd(new \DateTime('2013-12-31 11:00'));

        $this->trainTheAggregatorToReturn(array($timeblockA, $timeblockB));
        $timeblocks = $this->SUT->getSeparatedTimeblocks(
            new \DateTime('2013-09-30 00:00'),
            new \DateTime('2013-10-01 23:59')
        );

        $this->assertEquals('2013-09-30', $timeblocks[0]['date']->format('Y-m-d'), 'Date should be 2013-09-30.');
        $this->assertEquals(
            new \DateTime('2013-09-30 08:00'),
            $timeblocks[0]['begin'],
            'Begin should be 2013-09-30 08:00.'
        );

        $this->assertEquals('2013-09-30', $timeblocks[1]['date']->format('Y-m-d'), 'Date should be 2013-09-30.');
        $this->assertEquals(
            new \DateTime('2013-09-30 12:00'),
            $timeblocks[1]['begin'],
            'Begin should be 2013-09-30 12:00.'
        );

        $this->assertEquals('2013-10-01', $timeblocks[2]['date']->format('Y-m-d'), 'Date should be 2013-10-01.');
        $this->assertEquals(
            new \DateTime('2013-10-01 08:00'),
            $timeblocks[2]['begin'],
            'Begin should be 2013-10-01 08:00.'
        );

        $this->assertEquals('2013-10-01', $timeblocks[3]['date']->format('Y-m-d'), 'Date should be 2013-10-01.');
        $this->assertEquals(
            new \DateTime('2013-10-01 12:00'),
            $timeblocks[3]['begin'],
            'Begin should be 2013-10-01 12:00.'
        );
    }


    /**
     * Used to reduce method footprints and simplify code
     *
     * Returns a Timeblock with
     *  - IntervalBegin @ 2013-01-01
     *  - IntervalEnd   @ 2013-12-31
     *  - Begin         @ 12:00
     *  - End           @ 17:00
     *  - All Weekdays
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\Timeblock
     */
    protected function trainADefaultTimeblock()
    {
        $timeblock = new Timeblock();
        $timeblock->setWeekdays(1016)   // All Weekdays
            ->setIntervalBegin(new \DateTime('2013-01-01'))
            ->setIntervalEnd(new \DateTime('2013-12-31'))
            ->setBegin(new \DateTime('2013-01-01 12:00'))
            ->setEnd(new \DateTime('2013-12-31 17:00'));

        return $timeblock;
    }

    /**
     * Used to reduce method footprints and simplify code
     * Trains the self::aggregator to expect the method 'getTimeblocks' to return $timeblocks
     *
     * @param array $timeblocks
     */
    protected function trainTheAggregatorToReturn(array $timeblocks = array())
    {
        $this->aggregator->expects($this->once())
            ->method('getTimeblocks')
            ->will($this->returnValue($timeblocks));
    }
}
