<?php

namespace Oktolab\Bundle\RentBundle\Test\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockTransformer;

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

    public function setUp()
    {
        parent::setUp();

        $this->SUT = new TimeblockTransformer();
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockTransformer', $this->SUT);
    }

    public function testGetTransformedTimeblocksReturnsArray()
    {
        $this->assertEquals(array(), $this->SUT->getTransformedTimeblocks());
    }

    public function testGetTransformedTimeblocksThrowsExceptionOnInvalidEndDate()
    {
        $this->setExpectedException('\LogicException');
        $this->SUT->getTransformedTimeblocks(new \DateTime('now'), new \DateTime('-3 hours'));
    }
}
