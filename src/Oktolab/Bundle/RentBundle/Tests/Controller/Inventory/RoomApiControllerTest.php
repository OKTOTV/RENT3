<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * Description of RoomApiControllerTest
 *
 * @author rs
 */
class RoomApiControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function typeaheadPrefetchReturnsValidJsonResponse()
    {
        $this->client->request('GET', '/api/room/typeahead.json');

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->isCacheable(), 'Response is cacheable.');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');
    }

    /**
     * @test
     */
    public function typeaheadRemoteReturnsValidJsonResponse()
    {
        $this->client->request('GET', '/api/room/typeahead.json/room1');

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue(!$response->isCacheable(), 'Response is not cacheable.');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');
    }

    /**
     * @test
     */
    public function typeaheadRemoteReturnsRoom()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\RoomFixture'));
        $this->client->request('GET', '/api/room/typeahead.json/ASD');

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue(!$response->isCacheable(), 'Response is not cacheable.');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');

        $rooms = json_decode($response->getContent());

        $this->assertEquals(1, count($rooms));
        $this->assertEquals('RoomTitle', $rooms[0]->title);
        $this->assertEquals('room:1', $rooms[0]->value);
    }


/**
     * @test
     */
    public function typeaheadPrefetchReturnsRoom()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\RoomFixture'));
        $this->client->request('GET', '/api/room/typeahead.json');

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->isCacheable(), 'Response is cacheable.');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');

        $rooms = json_decode($response->getContent());

        $this->assertEquals(1, count($rooms));
        $this->assertEquals('RoomTitle', $rooms[0]->title);
        $this->assertEquals('room:1', $rooms[0]->value);
    }
}
