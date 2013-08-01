<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Event;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class RentControllerTest extends WebTestCase
{
    public function testRenderRentInventoryFormRendersCorrectly()
    {
        $this->client->request('GET', '/rent/inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertTrue($this->client->getResponse()->isCacheable(), 'Response should be cacheable');
    }

    public function testRenderRentRoomFormRendersCorrectly()
    {
        $this->client->request('GET', '/rent/room');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertTrue($this->client->getResponse()->isCacheable(), 'Response should be cacheable');
    }
}
