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
}
