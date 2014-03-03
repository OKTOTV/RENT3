<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * Description of SetApiControllerTest
 *
 * @author meh
 */
class SetApiControllerTest extends WebTestCase
{

    /**
     * @test
     */
    public function typeaheadPrefetchReturnsValidJsonResponse()
    {
        $this->client->request('GET', '/api/set/typeahead.json');

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->isCacheable(), 'Response is cacheable.');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');
    }

    /**
     * @test
     */
    public function typeaheadPrefetchReturnsValidTypeahadJSON()
    {
        $this->loadFixtures(array('\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\API\SetApiFixture'));
        $this->client->request('GET', '/api/set/typeahead.json');

        $response = $this->client->getResponse();
        $json = json_decode($response->getContent(), true);

        $this->assertEquals(1, count($json));
        $this->assertEquals('Testset1', $json[0]['displayName']);
        $this->assertEquals('Testset1', $json[0]['tokens'][0]);
        $this->assertEquals('Testset', $json[0]['tokens'][1]);
        $this->assertEquals('description', $json[0]['tokens'][2]);
        $this->assertEquals('APITESTSET1', $json[0]['tokens'][3]);
        $this->assertEquals('set', $json[0]['type']);
    }
}
