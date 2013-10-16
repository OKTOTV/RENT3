<?php

namespace Oktolab\Bundle\RentBundle\Tests\Validation;

use Oktolab\Bundle\RentBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of Event Validation
 *
 * @author meh
 *
 * @group Event
 */
class EventTest extends WebTestCase
{
    /**
     * @var Event
     */
    protected $SUT = null;

    /**
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator = null;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        self::$kernel = self::createKernel();
        self::$kernel->boot();

        $this->validator = self::$kernel->getContainer()->get('validator');

        $this->SUT = new Event();
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\Event', $this->SUT, 'Should be instance of Event');
    }

    /**
     * @test
     */
    public function validEvent()
    {
        $this->SUT->setName('My Event.')
            ->setBegin(new \DateTime('now'))
            ->setEnd(new \DateTime('+3 hours'));

        $errors = $this->validator->validate($this->SUT);
        $this->assertCount(0, $errors);
    }

    /**
     * @test
     */
    public function invalidEvent()
    {
        $this->SUT->setBegin(new \DateTime('now'))
            ->setEnd(new \DateTime('+3 hours'));

        $errors = $this->validator->validate($this->SUT);
        $this->assertCount(1, $errors);
        $this->assertSame('name', $errors[0]->getPropertyPath());
    }

    /**
     * @test
     */
    public function validEventDates()
    {
        $this->SUT->setName('My Event.')
            ->setBegin(new \DateTime('now'))
            ->setEnd(new \DateTime('+3 hours'));

        $errors = $this->validator->validate($this->SUT, array('Event', 'Logic'));
        $this->assertCount(0, $errors);
    }

    /**
     * @test
     */
    public function invalidEventDates()
    {
        $this->SUT->setName('My Event.')
            ->setBegin(new \DateTime('now'))
            ->setEnd(new \DateTime('-3 hours'));

        $errors = $this->validator->validate($this->SUT, array('Event', 'Logic'));
        $this->assertCount(1, $errors);
        $this->assertSame('endAfterBegin', $errors[0]->getPropertyPath());
    }
}
