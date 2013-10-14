<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Event;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Oktolab\Bundle\RentBundle\Entity\Event;

/**
 * Event Controller Test
 */
class EventControllerTest extends WebTestCase
{

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures(array('\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture'));
    }

    /**
     * @group Event
     * @test
     */
    public function createAnEventReturnsValidResponse()
    {
        $this->client->request('POST', '/event');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $this->assertFalse($this->client->getResponse()->isCacheable(), 'Response must not be cacheable.');


    }

    /**
     * @depends createAnEventReturnsValidResponse
     * @group   Event
     * @test
     */
    public function createAnEventWithItems()
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $itemA = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'F00B5R'));
        $itemB = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'B5ZF00'));

        $crawler = $this->client->request('GET', '/rent/inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $form = $crawler->filter('#OktolabRentBundle_Event_Form_update')->form(
            array(
                'OktolabRentBundle_Event_Form[name]'                => 'My Event',
                'OktolabRentBundle_Event_Form[description]'         => 'Some notices about this event. It is a test.',
                'OktolabRentBundle_Event_Form[begin]'               => '2013-10-11 12:00:00',
                'OktolabRentBundle_Event_Form[end]'                 => '2013-10-12 17:00:00',
            )
        );

        $values = $form->getPhpValues();
        $values['OktolabRentBundle_Event_Form']['objects'] = array(
            0 => array('object' => $itemA->getId(), 'type' => $itemA->getType()),
            1 => array('object' => $itemB->getId(), 'type' => $itemB->getType()),
        );

        // thx to: https://github.com/symfony/symfony/issues/4124#issuecomment-13229362
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect.');

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $event = $em->getRepository('OktolabRentBundle:Event')->findOneBy(array('id' => 1));
        $this->assertSame('My Event', $event->getName());
        $this->assertSame(Event::STATE_PREPARED, $event->getState(), 'State should be "PREPARED".');
        $this->assertEquals(new \DateTime('2013-10-11 12:00:00'), $event->getBegin());
        $this->assertEquals(new \DateTime('2013-10-12 17:00:00'), $event->getEnd());

        $eventObjects = $event->getObjects();
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\EventObject', $eventObjects[0]);
        $this->assertSame($itemA->getId(), $eventObjects[0]->getObject());
        $this->assertSame($itemA->getType(), $eventObjects[0]->getType());

        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\EventObject', $eventObjects[1]);
        $this->assertSame($itemB->getId(), $eventObjects[1]->getObject());
        $this->assertSame($itemB->getType(), $eventObjects[1]->getType());
    }

    /**
     * @depends createAnEventReturnsValidResponse
     * @group   Event
     * @test
     */
    public function createAnEventDisplaysErrorsIfFormInputIsInvalid()
    {
        $this->markTestIncomplete('Error handling not implemented.');
    }

    /**
     * @depends createAnEventReturnsValidResponse
     * @group   Event
     * @test
     */
    public function createAnEventDisplaysErrorIfEventObjectNotFound()
    {
        $this->markTestIncomplete('Validation not implemented.');
    }

    /**
     * @depends createAnEventReturnsValidResponse
     * @group   Event
     * @test
     */
    public function createAnEventAddLog()
    {
        $this->markTestIncomplete('How to monitor Log? Symfony-Profiler?');
    }
}
