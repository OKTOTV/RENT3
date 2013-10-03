<?php

namespace Oktolab\Bundle\RentBundle\Tests\Entity;

use Oktolab\Bundle\RentBundle\Entity\Timeblock;

/**
 * Description of TimeblockTest
 *
 * @author meh
 */
class TimeblockTest extends \PHPUnit_Framework_TestCase
{

    public function testWeekdaysAsArraySetZeroOnEmtpyValue()
    {
        $SUT = new Timeblock();
        $SUT->setWeekdays(1);

        $SUT->setWeekdaysAsArray(array());
        $this->assertSame(0, $SUT->getWeekdays());
    }

    /**
     * @dataProvider setWeekdaysProvider
     */
    public function testWeekdaysAsArray(array $input, $expected)
    {
        $SUT = new Timeblock();
        $SUT->setWeekdaysAsArray($input);
        $this->assertSame($expected, $SUT->getWeekdays());
    }

    public function testIsActiveOnDate()
    {
        $timeblock = new Timeblock();
        $timeblock->setIntervalBegin(new \DateTime('2013-01-01'))
            ->setIntervalEnd(new \DateTime('2013-12-31'))
            ->setWeekdays(1016); // all Weekdays

        $this->assertTrue($timeblock->isActiveOnDate(new \DateTime('2013-10-03')));
    }

    public function testIsNotActiveOnDate()
    {
        $timeblock = new Timeblock();
        $timeblock->setIntervalBegin(new \DateTime('2013-01-01'))
            ->setIntervalEnd(new \DateTime('2013-12-31'))
            ->setWeekdays(1016); // all Weekdays

        $this->assertFalse($timeblock->isActiveOnDate(new \DateTime('2012-01-01')));
    }

    public function testIsActiveOnWednesday()
    {
        $this->markTestIncomplete();
        $timeblock = new Timeblock();
        $timeblock->setIntervalBegin(new \DateTime('2013-01-01'))
            ->setIntervalEnd(new \DateTime('2013-12-31'))
            ->setWeekdays(Timeblock::WEEKDAY_WE);

        $this->assertTrue($timeblock->isActiveOnDate(new \DateTime('2013-10-02')));
        $this->assertFalse($timeblock->isActiveOnDate(new \DateTime('2013-10-03')));
        $this->assertFalse($timeblock->isActiveOnDate(new \DateTime('2013-10-01')));
    }

    /**
     * @dataProvider hasWeekdayAvailableProvider
     */
    public function testHasWeekdayAvailable($timeblockWeekdays, $weekday, $expected)
    {
        $timeblock = new Timeblock();
        $timeblock->setWeekdays($timeblockWeekdays); // all Weekdays

        $this->assertSame($expected, $timeblock->hasWeekdayAvailable($weekday));
    }

    public function setWeekdaysProvider()
    {
        return array(
            array(array(Timeblock::WEEKDAY_MO, Timeblock::WEEKDAY_WE), 40),
            array(array(Timeblock::WEEKDAY_MO, Timeblock::WEEKDAY_WE, Timeblock::WEEKDAY_FR), 168),
            array(
                array(
                    Timeblock::WEEKDAY_MO,
                    Timeblock::WEEKDAY_TU,
                    Timeblock::WEEKDAY_WE,
                    Timeblock::WEEKDAY_TH,
                    Timeblock::WEEKDAY_FR
                ),
                248
            ),
            array(array(Timeblock::WEEKDAY_SA), 256),
        );
    }

    public function hasWeekdayAvailableProvider()
    {
        return array(
            array(1016, Timeblock::WEEKDAY_WE, true),
            array(512, Timeblock::WEEKDAY_WE, false),
            array(512, Timeblock::WEEKDAY_SA, false),
            array(512, Timeblock::WEEKDAY_SO, true),
            array(768, Timeblock::WEEKDAY_SA, true),
            array(40, Timeblock::WEEKDAY_TU, false),
            array(32, Timeblock::WEEKDAY_WE, true),
            array(8, Timeblock::WEEKDAY_MO, true),
        );
    }
}
