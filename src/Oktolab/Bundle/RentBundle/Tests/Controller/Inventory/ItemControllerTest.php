<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class ItemControllerTest extends WebTestCase
{

    private $entityManager;
    private $purger;

    public function setUp() {
        parent::setUp();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
        $this->purger = new ORMPurger($this->entityManager);
        $this->purger->purge();
    }

    public function testShowEmptyList()
    {
        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/item');
        $client->followRedirect();
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /inventory/item/"
        );
        $this->assertEquals(
            0,
            $crawler->filter('table tr')->count(),
            "This List should be empty"
        );
    }

    public function testCreateItem()
    {
        // Create a new client to browse the application
        $client = $this->client;

        // Create a new entry in the database
        $crawler = $client->request('GET', '/inventory/item/');

        $crawler = $client->click($crawler->selectLink('Neues Item')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Speichern')->form(
            array(
            'oktolab_bundle_rentbundle_inventory_itemtype[title]'  => 'Test',
            'oktolab_bundle_rentbundle_inventory_itemtype[description]' => 'Description',
            'oktolab_bundle_rentbundle_inventory_itemtype[barcode]' => 'ASDF01'
            )
        );

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        // Check data in the show view
        $this->assertGreaterThan(
            0,
            $crawler->filter('.aui-page-header-main:contains("Test")')->count(),
            'Missing element td:contains("Test")'
        );
    }

    public function testEditItem()
    {
        $itemFixtureLoader = new ItemFixture();
        $itemFixtureLoader->load($this->entityManager);

       //die(var_dump($item));
        $client = $this->client;
        // Edit the entity
        $crawler = $client->request('GET', 'inventory/item/1');
        $crawler = $client->click($crawler->selectLink('Editieren')->link());

        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]'  => 'Foo',
            )
        );

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(
            0,
            $crawler->filter('.aui-page-header-main:contains("Foo")')->count(),
            'Missing element [value="Foo"]'
        );
    }

    public function testEditErrorItem()
    {
        $itemFixtureLoader = new ItemFixture();
        $itemFixtureLoader->load($this->entityManager);

        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/item/1');
        $crawler = $client->click($crawler->selectLink('Editieren')->link());

        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]' => ''
            )
        );

        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('.error:contains("Du musst einen Titel angeben")')->count());
    }

    public function testDeleteItem()
    {
        $itemFixtureLoader = new ItemFixture();
        $itemFixtureLoader->load($this->entityManager);

        $client = $this->client;
        // Delete the entity,
        $crawler = $client->request('GET', 'inventory/item/1');
        $crawler = $client->click($crawler->selectLink('Editieren')->link());

        $client->click($crawler->selectLink('Löschen')->link());
        //$crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/ItemTitle0/', $client->getResponse()->getContent());
    }

    public function testShowListWith4Items()
    {
        $itemFixtureLoader = new ItemFixture();
        $itemFixtureLoader->load($this->entityManager, 3);

        $client = $this->client;

        $client->request('GET', 'inventory/item');
        $crawler = $client->followRedirect();

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /inventory/item/"
        );
        $this->assertEquals(
            4,
            $crawler->filter('table tr')->count(),
            "This List should NOT be empty"
        );
    }
}
