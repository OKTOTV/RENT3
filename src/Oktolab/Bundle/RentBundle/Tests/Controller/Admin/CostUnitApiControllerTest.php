<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Admin;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * CostUnitApiControllerTest for typeahead costunit api
 *
 * @author rs
 */
class CostUnitApiControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function typeaheadPrefetchReturnsValidJsonResponse()
    {
        $this->client->request('GET', '/api/costunit/typeahead.json');


        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($response->getContent(), 'Response sends valid JSON.');
    }

    /**
     * @test
     */
    public function typeaheadPrefetchReturnsValidTypeaheadJSON()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\API\CostUnitApiFixture'));
        $this->client->request('GET', '/api/costunit/typeahead.json');

        $response = $this->client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $json = json_decode($response->getContent(), true);

        $this->assertEquals(1, count($json));
        $this->assertEquals('Test costunit', $json[0]['displayName']);
        $this->assertEquals('Test', $json[0]['tokens'][0]);
        $this->assertEquals('costunit', $json[0]['tokens'][1]);
        $this->assertEquals('TC', $json[0]['tokens'][2]);
    }
}
