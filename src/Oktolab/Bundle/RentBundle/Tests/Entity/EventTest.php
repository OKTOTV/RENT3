<?php

namespace Oktolab\Bundle\RentBundle\Tests\Entity;

use Oktolab\Bundle\RentBundle\Entity\Event;

class EventTest extends \PHPUnit_Framework_TestCase
{

    /**
     *  @expectedException \InvalidArgumentException
     */
    public function testEndDateMustBeGreaterThanStartDate()
    {
        $event = new Event();
        $event->setBegin(new \DateTime('now'));
        $event->setEnd(new \DateTime('-3 hours'));
    }
}
