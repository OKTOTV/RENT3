<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Event;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * Event Controller Test
 */
class EventControllerTest extends WebTestCase
{

    /**
     * @test
     */
    public function createAnEventWithItems()
    {
        $this->markTestIncomplete('Need Item Fixtures for this');
        $this->loadFixtures(array());

        $crawler = $this->client->request('GET', '/rent/inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $form = $crawler->filter('#OktolabRentBundle_Event_Form_update')->form(
            array(
                'OktolabRentBundle_Event_Form[name]'                => 'My Event',
                'OktolabRentBundle_Event_Form[description]'         => 'Some notices about this event. It is a test.',
                'OktolabRentBundle_Event_Form[begin]'               => '2013-10-11 12:00:00',
                'OktolabRentBundle_Event_Form[end]'                 => '2013-10-12 17:00:00',
                'OktolabRentBundle_Event_Form[objects][0][object]'  => '3',
                'OktolabRentBundle_Event_Form[objects][0][type]'    => 'item',
                'OktolabRentBundle_Event_Form[objects][1][object]'  => '5',
                'OktolabRentBundle_Event_Form[objects][1][type]'    => 'item',
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect.');

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        // $em->getRepository('OktolabRentBundle:Event')->findOne(array('id' => 1));
    }
}
