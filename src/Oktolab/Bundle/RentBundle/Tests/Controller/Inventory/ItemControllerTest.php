<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class ItemControllerTest extends WebTestCase
{
    
    public function testShowList()
    {
        $this->markTestIncomplete();
    }

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = $this->client;

        // Create a new entry in the database
        $crawler = $client->request('GET', '/inventory/item/');        
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /inventory/item/");
        $crawler = $client->click($crawler->selectLink('Neues Item')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Senden')->form(array(
            'oktolab_bundle_rentbundle_inventory_itemtype[title]'  => 'Test',
            'oktolab_bundle_rentbundle_inventory_itemtype[description]' => 'Description',
            'oktolab_bundle_rentbundle_inventory_itemtype[barcode]' => 'ASDF01',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Edit')->form(array(
            'oktolab_bundle_rentbundle_inventory_itemtype[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }
}
