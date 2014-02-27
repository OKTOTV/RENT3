<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Event;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * Rent Controller Test
 */
class RentControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function rentInventoryResponse()
    {
        $this->loadFixtures(
            array(
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
            )
        );
        $this->client->request('GET', '/rent/inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertTrue($this->client->getResponse()->isCacheable(), 'Response should be cacheable');
    }

    /**
     * @test
     * @depends rentInventoryResponse
     */
    public function rentInventoryRendersAsFragment()
    {
        $this->loadFixtures(
            array(
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
            )
        );
        $crawler = $this->client->request('GET', '/rent/inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $this->assertSame(0, $crawler->filterXPath('body')->count(), 'No body should be rendered.');
        $this->assertSame(1, $crawler->filter('div#rent-inventory-form')->count(), '#rent-inventory-form expected.');

        $form = $crawler->filter('div#rent-inventory-form form');
        $this->assertSame(1, $form->count(), 'Form was expected to be rendered.');
        $this->assertEquals('post', $form->attr('method'), 'Form method was expected to be "POST"');

        $url = $this->client->getContainer()->get('router')->generate('OktolabRentBundle_Event_Create');
        $this->assertEquals($url, $form->attr('action'), sprintf('Form action was expected to be "%s".', $url));
    }

    public function testRenderRentRoomFormRendersCorrectly()
    {
        $this->loadFixtures(
            array(
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
            )
        );
        $this->client->request('GET', '/rent/room');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertTrue($this->client->getResponse()->isCacheable(), 'Response should be cacheable');
    }
}
