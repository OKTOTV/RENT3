<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * ItemApiController tests the json and the urls responding to typeahead
 *
 * @author rs
 */
class ItemApiControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function typeaheadPrefetchReturnsValidJsonResponse()
    {
        $this->client->request('GET', '/api/item/typeahead.json');

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->isCacheable(), 'Response is cacheable.');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');
    }

    /**
     * @test
     */
    public function typeaheadPrefetchReturnsValidTypeaheadJSON()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\API\ItemApiFixture'
            )
        );
        $this->client->request('GET', '/api/item/typeahead.json');
        $response = $this->client->getResponse();

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $json = json_decode($response->getContent(), true);

        $this->assertEquals(1, count($json));
        $this->assertEquals('Testitem1', $json[0]['displayName']);
        $this->assertEquals('item', $json[0]['type']);
        $this->assertEquals('Testitem1 description', $json[0]['description']);
        $this->assertEquals('APITEST1', $json[0]['barcode']);
        $this->assertEquals(2, count($json[0]['tokens']));
        $this->assertEquals('Testitem1', $json[0]['tokens'][0]);
        $this->assertEquals('APITEST1', $json[0]['tokens'][1]);
    }
}
