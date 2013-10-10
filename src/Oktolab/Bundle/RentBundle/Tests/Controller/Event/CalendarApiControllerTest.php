<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Event;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * Calendar API Controller Tests
 */
class CalendarApiControllerTest extends WebTestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\EventApiTimeblockFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\EventApiInventoryFixture',
            )
        );
    }

    /**
     * @test
     */
    public function inventoryActionReturnsValidJsonResponse()
    {
        $response = $this->requestXmlHttp('/api/calendar/inventory.json');
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');
    }

    /**
     * @depends inventoryActionReturnsValidJsonResponse
     * @test
     */
    public function inventoryActionReturnsCacheableResponse()
    {
        $response = $this->requestXmlHttp('/api/calendar/inventory.json');
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->isCacheable(), 'Response is cacheable.');
    }

    /**
     * @depends inventoryActionReturnsValidJsonResponse
     * @test
     */
    public function inventoryActionReturnsInventoryAsJson()
    {
        $response = $this->requestXmlHttp('/api/calendar/inventory.json');
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');

        $inventory = $this->client->getContainer()->get('oktolab.event_calendar_inventory')->getTransformedInventory();
        $this->assertCount(1, $inventory, 'One Category was aggregated');
        $this->assertCount(2, $inventory[0], 'Two Items were aggregated in first Category');
        $this->assertJsonStringEqualsJsonString(
            json_encode($inventory),
            $response->getContent(),
            'Response matches JSON from database'
        );
    }

    /**
     * @test
     */
    public function timeblockActionReturnsValidJsonResponse()
    {
        $response = $this->requestXmlHttp('/api/calendar/timeblock.json');
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');
    }

    /**
     * @depends timeblockActionReturnsValidJsonResponse
     * @test
     */
    public function timeblockActionReturnsCacheableResponse()
    {
        $response = $this->requestXmlHttp('/api/calendar/timeblock.json');
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->isCacheable(), 'Response is cacheable.');
    }

    /**
     * @depends timeblockActionReturnsValidJsonResponse
     * @test
     */
    public function timeblockActionReturnsTimeblocksAsJson()
    {
        $response = $this->requestXmlHttp('/api/calendar/timeblock.json');
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertJson($response->getContent(), 'Response returns valid JSON.');

        $timeblocks = $this->client->getContainer()
            ->get('oktolab.event_calendar_timeblock')
            ->getTransformedTimeblocks(new \DateTime('today 00:00'), new \DateTime('+30 days 00:00'));

        $this->assertCount(30, $timeblocks);
        $this->assertJsonStringEqualsJsonString(
            json_encode($timeblocks),
            $response->getContent(),
            'Response matches aggregated/transformed JSON from database'
        );
    }

    /**
     * @test
     */
    public function eventActionReturnsValidJsonResponse()
    {
        $response = $this->requestXmlHttp('/api/calendar/events.json');
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response returns valid JSON.');
    }

    /**
     * @test
     */
    public function eventActionReturnsEventsAsJson()
    {
        $this->markTestIncomplete('Test if EventAction returns Events from @oktolab.event_calendar_event.');
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
