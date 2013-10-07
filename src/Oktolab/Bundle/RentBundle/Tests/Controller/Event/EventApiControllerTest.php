<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Event;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * Event API Controller Tests
 */
class EventApiControllerTest extends WebTestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures(array('\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\EventApiTimeblockFixture'));
    }

    /**
     * @test
     */
    public function inventoryActionReturnsValidJsonResponse()
    {
        $response = $this->requestXmlHttp('/api/event/inventory.json');
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');
    }

    /**
     * @depends inventoryActionReturnsValidJsonResponse
     * @test
     */
    public function testInventoryActionReturnsCacheableResponse()
    {
        $response = $this->requestXmlHttp('/api/event/inventory.json');
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->isCacheable(), 'Response is cacheable.');
    }

    /**
     * @depends inventoryActionReturnsValidJsonResponse
     * @test
     */
    public function inventoryActionReturnsInventoryAsJson()
    {
        $response = $this->requestXmlHttp('/api/event/inventory.json');
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');

        $inventory = $this->client->getContainer()->get('oktolab.event_calendar_inventory')->getTransformedInventory();
        $this->assertJsonStringEqualsJsonString(
            json_encode($inventory),
            $response->getContent(),
            'Response matches JSON from database'
        );
    }

    /**
     * Simple wrapper to make a JSON-Request
     *
     * @param string $uri
     * @param string $method
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function requestXmlHttp($uri, $method = 'GET')
    {
        $this->client->request(
            $method,
            $uri,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'HTTP_X-Requested-With' => 'XMLHttpRequest')
        );

        return $this->client->getResponse();
    }
}
