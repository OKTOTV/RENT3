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
        $this->assertCount(2, $inventory, 'One Category was aggregated');
        $this->assertCount(2, $inventory[1], 'Two Items were aggregated in first Category');
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
            ->getTransformedTimeblocks(new \DateTime('today 00:00'), new \DateTime('+7 days 00:00'));
        $this->assertCount(8, $timeblocks);
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
    public function calendarInventoryEvents()
    {
        $this->loadFixtures(array(
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Calendar\InventoryCalendarEventFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Calendar\RoomCalendarEventFixture'
        ));
        $response = $this->requestXmlHttp('/api/calendar/events.json/2013-10-13/2013-10-20');
        $this->assertTrue($response->isSuccessful(), 'Response is successful');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response returns valid JSON.');

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(1, count($json));
    }

    /**
     * @test
     */
    public function calendarRoomEvents()
    {
        $this->loadFixtures(array(
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Calendar\InventoryCalendarEventFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Calendar\RoomCalendarEventFixture'
        ));
        $response = $this->requestXmlHttp('/api/calendar/room_events.json/2013-10-13/2013-10-20');
        $this->assertTrue($response->isSuccessful(), 'Response is successful');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response returns valid JSON.');

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(1, count($json));
    }

    /**
     * @test
     */
    public function calendarRoomWeekTimeblocks()
    {
        $this->loadFixtures(array(
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Calendar\RoomTimeblockFixture'
        ));
        $response = $this->requestXmlHttp('/api/calendar/room_timeblock.json/2014-04-01/2014-04-07');
        $this->assertTrue($response->isSuccessful(), 'Response is successful');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response returns valid JSON.');

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(7, count($json));
        $this->assertEquals('Di, 01.04', $json['2014-04-01T00:00:00+02:00']['title']);
        $this->assertEquals(1, count($json['2014-04-01T00:00:00+02:00']['blocks']));
    }

    /**
     * @test
     */
    public function calendarRoomDayTimeblocks()
    {
        $this->loadFixtures(array(
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Calendar\RoomTimeblockFixture'
        ));
        $response = $this->requestXmlHttp('/api/calendar/room_day_timeblock.json/2014-04-01T00:00:00/2014-04-01T23:59:00');
        $this->assertTrue($response->isSuccessful(), 'Response is successful');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response returns valid JSON.');

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(1, count($json));
        $this->assertEquals('', $json['2014-04-01T00:00:00+02:00']['title']);
        $this->assertEquals(19, count($json['2014-04-01T00:00:00+02:00']['blocks']));
        $this->assertEquals('2014-04-01T13:00:00+02:00', $json['2014-04-01T00:00:00+02:00']['blocks'][0]['begin']);
        $this->assertEquals('2014-04-01T13:30:00+02:00', $json['2014-04-01T00:00:00+02:00']['blocks'][0]['end']);
        $this->assertEquals('2014-04-01T21:30:00+02:00', $json['2014-04-01T00:00:00+02:00']['blocks'][17]['begin']);
        $this->assertEquals('2014-04-01T22:00:00+02:00', $json['2014-04-01T00:00:00+02:00']['blocks'][17]['end']);
        $this->assertEquals('2014-04-01T22:00:00+02:00', $json['2014-04-01T00:00:00+02:00']['blocks'][18]['begin']);
        $this->assertEquals('2014-04-01T22:30:00+02:00', $json['2014-04-01T00:00:00+02:00']['blocks'][18]['end']);
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
