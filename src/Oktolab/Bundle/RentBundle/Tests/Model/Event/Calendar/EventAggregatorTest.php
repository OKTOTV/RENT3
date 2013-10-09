<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\EventAggregator;

/**
 * Description of EventAggregatorTest
 *
 * @author meh
 */
class EventAggregatorTest extends \PHPUnit_Framework_TestCase
{
    protected $SUT = null;

    public function setUp()
    {
        parent::setUp();

        $this->SUT = new EventAggregator();
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\EventAggregator', $this->SUT);
    }

    public function testActiveEventsThrowsRepositoryNotFoundException()
    {
        $this->setExpectedException('\Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException');
        $this->SUT->getActiveEvents(new \DateTime());
    }
}
