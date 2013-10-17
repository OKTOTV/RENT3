<?php

namespace Oktolab\Bundle\RentBundle\Tests\Entity;

use Oktolab\Bundle\RentBundle\Entity\Event;

/**
 * @group Event
 */
class EventTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Oktolab\Bundle\RentBundle\Entity\Event
     */
    protected $SUT = null;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->SUT = new Event();
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\Event', $this->SUT);
    }

    /**
     * @test
     */
    public function endMustBeGreaterThanBegin()
    {
        $this->SUT->setBegin(new \DateTime('+3 hours'))
            ->setEnd(new \DateTime('now'));

        $this->assertFalse($this->SUT->isEndAfterBegin());
    }

    /**
     * @test
     */
    public function endIsGreaterThanBegin()
    {
        $this->SUT->setBegin(new \DateTime('now'))
            ->setEnd(new \DateTime('+3 hours'));

        $this->assertTrue($this->SUT->isEndAfterBegin());
    }

    /**
     * @test
     */
    public function getStateDefaultBehaviour()
    {
        $this->SUT->setState(Event::STATE_LENT); // 2
        $this->assertSame(2, $this->SUT->getState(), 'State should be "int:2, const:STATE_LENT".');
    }

    /**
     * @test
     * @depends getStateDefaultBehaviour
     * @dataProvider getStateNamesProvider
     */
    public function getStateWithNames($state, $expected, $message)
    {
        $this->SUT->setState($state);
        $this->assertSame($expected, $this->SUT->getState(true), $message);
    }

    public function getStateNamesProvider()
    {
        return array(
            array(Event::STATE_LENT, 'LENT', 'State should be const:STATE_LENT.'),
            array(Event::STATE_CANCELED, 'CANCELED', 'State should be const:STATE_CANCELED.'),
            array(Event::STATE_DELIVERED, 'DELIVERED', 'State should be const:STATE_DELIVERED.'),
            array(Event::STATE_PREPARED, 'PREPARED', 'State should be const:STATE_PREPARED.'),
        );
    }
}
