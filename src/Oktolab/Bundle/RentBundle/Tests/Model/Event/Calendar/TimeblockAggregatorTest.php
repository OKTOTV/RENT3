<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator;
use Oktolab\Bundle\RentBundle\Entity\Timeblock;
use Oktolab\Bundle\RentBundle\Entity\EventType;

class TimeblockAggregatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System Under Test
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator
     */
    protected $SUT = null;

    public function setup()
    {
        parent::setup();

        $this->SUT = new TimeblockAggregator();
        $this->assertInstanceOf('Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator', $this->SUT);
    }

    public function testAggregateTimeblocksReturnsArray()
    {
        $timeblock = new Timeblock();
        $eventType = new EventType();

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())->method('findBy')->will($this->returnValue(array($timeblock)));

        $repository2 = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository2->expects($this->once())->method('findOneBy')->will($this->returnValue($eventType));

        $this->SUT->addRepository('Timeblock', $repository);
        $this->SUT->addRepository('EventType', $repository2);
        $this->assertSame(array($timeblock), $this->SUT->getTimeblocks());
    }

    public function testAggregateTimeblocksThrowsExceptionOnEmptyRepository()
    {
        $this->setExpectedException('\Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException');
        $this->SUT->getTimeblocks();
    }
}
