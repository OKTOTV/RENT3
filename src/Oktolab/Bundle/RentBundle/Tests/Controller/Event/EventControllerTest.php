<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Event;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class EventControllerTest extends WebTestCase
{
    public function testEventApiSendsEventsAsJson()
    {
        $this->markTestSkipped();
        $this->client->request(
            'GET',
            '/api/v1/events.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'HTTP_X-Requested-With' => 'XMLHttpRequest')
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertNotNull(json_decode($response->getContent()), 'Can decode JSON Array');

        $this->markTestIncomplete('needs event fixtures');
        //$this->assertJsonStringEqualsJsonString()
    }

    public function testEventApiSendsCalendarConfigurationAsJson()
    {
        $this->markTestSkipped();
        $this->client->request(
            'GET',
            '/api/v1/calendarConfiguration.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'HTTP_X-Requested-With' => 'XMLHttpRequest')
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertNotNull(json_decode($response->getContent()), 'Can decode JSON Array');
    }

    public function testEventApiCalendarConfigurationContainsDefinedStructure()
    {
        $this->markTestSkipped();
        $this->client->request(
            'GET',
            '/api/v1/calendarConfiguration.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'HTTP_X-Requested-With' => 'XMLHttpRequest')
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful');

        $json = (array) json_decode($response->getContent());
        $this->assertArrayHasKey('dates', $json, 'Array has key "dates"');
        $this->assertArrayHasKey('items', $json, 'Array has key "items"');

        foreach ($json['dates'] as $date) {
            $this->assertArrayHasKey('timeblocks', (array) $date);
            $this->assertArrayHasKey('date', (array) $date);
        }
    }
}
