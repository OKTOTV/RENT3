<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Event;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * Description of EventApiControllerTest
 *
 * @author rt
 * @group Event
 */
class EventApiControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function categoryApiResponse()
    {
        $this->loadFixtures(array(
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\API\CategoryApiFixture'
        ));

        $this->client->request('GET', '/api/event/category/typeahead.json/2013-10-14T11:00:00/2013-10-15T17:00:00');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertJson($this->client->getResponse()->getContent(), 'Response sends valid JSON.');

        $items = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals(1, count($items));
        $this->assertEquals('asdf', $items[0]->barcode);

        $this->assertEquals('Category', $items[0]->tokens[1]);
    }

}

?>
