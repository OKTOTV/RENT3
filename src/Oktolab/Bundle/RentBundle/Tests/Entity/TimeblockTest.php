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
}
