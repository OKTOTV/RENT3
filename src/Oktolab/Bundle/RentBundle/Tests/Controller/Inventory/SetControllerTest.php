<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class SetControllerTest extends WebTestCase
{
    public function testShowEmptySetList()
    {
        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/set');
        $client->followRedirect();
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET inventory/set/"
        );

        $this->assertEquals(
            0,
            $crawler->filter('table tr')->count(),
            "There should be no sets in this list"
        );
    }

    public function testCreateSet()
    {
        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/set/new');

        $form = $crawler->selectButton('Speichern')->form(
            array(
            'oktolab_bundle_rentbundle_inventory_settype[title]' => 'TestSet',
            'oktolab_bundle_rentbundle_inventory_settype[description]' => 'TestDescription'
            )
        );

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('.aui-page-header-main:contains("TestSet")')->count(),
            'There should be the set name on this page header'
        );
    }

    public function testEditSet()
    {
        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/set/1/edit');

        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_settype[title]'  => 'Foo'
            )
        );

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('.aui-page-header-main:contains("Foo")')->count(),
            'The page header should contain the new name [value="Foo"]'
        );
    }

    public function testEditErrorSet()
    {
        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/set/1/edit');

        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_settype[title]' => ''
            )
        );

        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("Du musst einen Titel angeben")')->count());
    }

    public function testDeleteSet()
    {
        $client = $this->client;
        // Delete the entity,
        $crawler = $client->request('GET', 'inventory/set/1/edit');

        $client->click($crawler->selectLink('Löschen')->link());

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }

    public function testAddItemToSet()
    {
        $this->markTestIncomplete(
            'Implement test after action completion'
        );
    }

    public function testRemoveItemFromSet()
    {
        $this->markTestIncomplete(
            'Implement test after action completion'
        );
    }

    public function testDeleteSetWithAttachedItems()
    {
        $this->markTestIncomplete(
            'Implement test after action completion'
        );
    }
}
