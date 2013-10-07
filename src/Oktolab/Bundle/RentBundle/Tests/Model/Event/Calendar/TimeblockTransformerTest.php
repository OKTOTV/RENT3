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

    /**
     * @var \Doctrine\Common\Cache\ArrayCache
     */
    protected $cache = null;

    public function setUp()
    {
        parent::setUp();

        $this->aggregator = $this->getMock('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator');
        $this->cache = new ArrayCache();
        $this->SUT = new TimeblockTransformer($this->aggregator, $this->cache);
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockTransformer', $this->SUT);
    }

    public function testTransformedTimeblocksThrowsExceptionOnInvalidEndDate()
    {
        $this->setExpectedException('\LogicException');
        $this->SUT->getTransformedTimeblocks(new \DateTime('now'), new \DateTime('-3 hours'));
    }

    public function testTransformedTimeblocksReturnsFormattedArray()
    {
        $date = new \DateTime('2013-10-07');
        $expected = array(
            'date'  => $date->format('c'),  // equals 2013-10-07T00:00:00+02:00
            'begin' => $date->modify('2013-10-07 12:00')->format('c'),
            'end'   => $date->modify('2013-10-07 17:00')->format('c'),
        );

        $timeblocks = array(
            array(
                'begin'     => new \DateTime('2013-10-07 12:00'),
                'end'       => new \DateTime('2013-10-07 17:00'),
                'date'      => new \DateTime('2013-10-07 00:00'),
                'timeblock' => $this->trainADefaultTimeblock(),
            )
        );

        $SUT = $this->getMockBuilder('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockTransformer')
            ->setMethods(array('getSeparatedTimeblocks'))
            ->setConstructorArgs(array($this->aggregator, new ArrayCache()))
            ->getMock();

        $SUT->expects($this->once())->method('getSeparatedTimeblocks')->will($this->returnValue($timeblocks));
        $this->assertEquals(
            array($expected),
            $SUT->getTransformedTimeblocks(new \DateTime('2013-10-07 00:00'), new \DateTime('2013-10-08 00:00'))
        );
    }

    public function testTransformedTimeblocksFetchesCache()
    {
        $cacheId = sprintf('%s::279', TimeblockTransformer::CACHE_ID);  // 2013-10-07 is the 279. day of year
        $this->cache->save($cacheId, array('begin' => '2013-10-07T00:00:00+02:00'));

//        var_dump(date('z')); die();
        $this->assertTrue($this->cache->contains($cacheId));
        $this->assertSame(
            $this->cache->fetch($cacheId),
            $this->SUT->getTransformedTimeblocks(
                new \DateTime('2013-10-07T00:00:00+02:00'),
                new \DateTime('2013-10-08')
            ),
            'Cached-Data can be fetched by TimeblockTransformer'
        );
    }

    public function testTransformedTimeblocksStoresResultInCache()
    {
        $cacheId = sprintf('%s::279', TimeblockTransformer::CACHE_ID); // 2013-10-07 is the 279. day of year
        $this->assertFalse($this->cache->contains($cacheId));

        $date = new \DateTime('2013-10-07 00:00:00');
        $expected = array(
            array(
                'begin' => $date->modify('12:00')->format('c'),
                'end'   => $date->modify('17:00')->format('c'),
                'date'  => $date->modify('00:00')->format('c'),
            )
        );

        $timeblocks = array(
            array(
                'begin'     => new \DateTime('2013-10-07 12:00'),
                'end'       => new \DateTime('2013-10-07 17:00'),
                'date'      => new \DateTime('2013-10-07 00:00'),
                'timeblock' => $this->trainADefaultTimeblock(),
            )
        );

        $SUT = $this->getMockBuilder('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockTransformer')
            ->setMethods(array('getSeparatedTimeblocks'))
            ->setConstructorArgs(array($this->aggregator, $this->cache))
            ->getMock();
        $SUT->expects($this->once())->method('getSeparatedTimeblocks')->will($this->returnValue($timeblocks));

        $SUT->getTransformedTimeblocks(new \DateTime('2013-10-07T00:00:00+02:00'), new \DateTime('2013-10-08'));
        $this->assertTrue($this->cache->contains($cacheId));
        $this->assertSame($expected, $this->cache->fetch($cacheId));
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

    public function testSortTimeblocksForSameDay()
    {
        $timeblocks = array(
            array(
                'begin'     => new \DateTime('2013-10-07 12:00'),
                'end'       => new \DateTime('2013-10-07 17:00'),
                'date'      => new \DateTime('2013-10-07 00:00'),
                'timeblock' => $this->trainADefaultTimeblock(),
            ),
            array(
                'begin'     => new \DateTime('2013-10-07 08:00'),
                'end'       => new \DateTime('2013-10-07 11:00'),
                'date'      => new \DateTime('2013-10-07 00:00'),
                'timeblock' => $this->trainADefaultTimeblock(),
            )
        );

        $sortedArray = $this->SUT->sortTimeblocks($timeblocks);
        $this->assertCount(2, $sortedArray);

        $this->assertArrayHasKey('begin', $sortedArray[0]);
        $this->assertEquals(new \DateTime('2013-10-07 08:00'), $sortedArray[0]['begin']);

        $this->assertArrayHasKey('begin', $sortedArray[1]);
        $this->assertEquals(new \DateTime('2013-10-07 12:00'), $sortedArray[1]['begin']);
    }

    public function testSortTimeblocksForDifferentDays()
    {
        $timeblocks = array(
            array(
                'begin'     => new \DateTime('2013-10-06 12:00'),
                'end'       => new \DateTime('2013-10-06 17:00'),
                'date'      => new \DateTime('2013-10-06 00:00'),
                'timeblock' => $this->trainADefaultTimeblock(),
            ),
            array(
                'begin'     => new \DateTime('2013-10-07 08:00'),
                'end'       => new \DateTime('2013-10-07 11:00'),
                'date'      => new \DateTime('2013-10-07 00:00'),
                'timeblock' => $this->trainADefaultTimeblock(),
            )
        );

        $sortedArray = $this->SUT->sortTimeblocks($timeblocks);
        $this->assertCount(2, $sortedArray);

        $this->assertArrayHasKey('begin', $sortedArray[0]);
        $this->assertEquals(new \DateTime('2013-10-06 12:00'), $sortedArray[0]['begin']);

        $this->assertArrayHasKey('begin', $sortedArray[1]);
        $this->assertEquals(new \DateTime('2013-10-07 08:00'), $sortedArray[1]['begin']);
    }

    public function testRebuildTimeblock()
    {
        $expectedTimeblock = array(
            'begin'         => new \DateTime('2013-10-07 12:00'),
            'end'           => new \DateTime('2013-10-07 17:00'),
            'date'          => new \DateTime('2013-10-07 00:00'),
            'timeblock'     => $this->trainADefaultTimeblock(),
        );

        $this->assertEquals(
            $expectedTimeblock,
            $this->SUT->rebuildTimeblock(new \DateTime('2013-10-07 00:00'), $this->trainADefaultTimeblock())
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
