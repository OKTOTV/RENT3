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

    public function testSetWeekdaysAsArraySetZeroOnEmtpyValue()
    {
        $this->markTestIncomplete('RT Training');
    }

    /**
     * @dataProvider setWeekdaysProvider
     */
    public function testSetWeekdaysAsArray(array $input, $expected)
    {
        $this->markTestIncomplete('RT Training');
    }

    public function setWeekdaysProvider()
    {
        return array(
            array(array(Timeblock::WEEKDAY_MO, Timeblock::WEEKDAY_WE), 3),
        );
    }
}
