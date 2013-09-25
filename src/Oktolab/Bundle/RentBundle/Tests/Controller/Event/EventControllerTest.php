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

    public function testEventApiSendsCalendarConfigurationAsValidJson()
    {
        $this->loadFixtures(
            array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\EventApiCalendarConfigurationFixture')
        );

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

        $json = json_decode($response->getContent(), true);
        $this->assertNotNull($json, 'Can decode JSON Array');
        $this->assertArrayHasKey('items', $json, 'JSON Array contains Objectives-Key');
        $this->assertArrayHasKey('dates', $json, 'JSON Array contains Dates-Key');
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

        $json = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('dates', $json, 'Array has key "dates"');
        $this->assertArrayHasKey('items', $json, 'Array has key "items"');

        foreach ($json['dates'] as $date) {
            $this->assertArrayHasKey('timeblocks', (array) $date);
            $this->assertArrayHasKey('date', (array) $date);
        }
    }
}
