<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ItemControllerTest extends WebTestCase
{

    private $entityManager;
    private $purger;

    public function setUp()
    {
        parent::setUp();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->purger = new ORMPurger($this->entityManager);
        $this->purger->purge();
    }

    public function testShowEmptyList()
    {
        $crawler = $this->client->request('GET', 'inventory/item');
        $this->client->followRedirect();
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
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
        // Create a new entry in the database
        $crawler = $this->client->request('GET', '/inventory/item/');

        $crawler = $this->client->click($crawler->selectLink('Neues Item')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Speichern')->form(
            array(
            'oktolab_bundle_rentbundle_inventory_itemtype[title]'  => 'Test',
            'oktolab_bundle_rentbundle_inventory_itemtype[description]' => 'Description',
            'oktolab_bundle_rentbundle_inventory_itemtype[barcode]' => 'ASDF01'
            )
        );

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
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

        $crawler = $this->client->request('GET', '/inventory/item/1');
        $crawler = $this->client->click($crawler->selectLink('Editieren')->link());

        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]'  => 'Foo',
            )
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('.aui-page-header-main:contains("Foo")')->count(),
            'Missing element [value="Foo"]'
        );
    }

    public function testEditItemThrowsErrorOnInvalidFormData()
    {
        $itemFixtureLoader = new ItemFixture();
        $itemFixtureLoader->load($this->entityManager);

        $crawler = $this->client->request('GET', '/inventory/item/1');
        $crawler = $this->client->click($crawler->selectLink('Editieren')->link());

        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]' => ''
            )
        );

        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('.error:contains("Du musst einen Titel angeben")')->count());
    }

    public function testDeleteItem()
    {
        $itemFixtureLoader = new ItemFixture();
        $itemFixtureLoader->load($this->entityManager);

        $crawler = $this->client->request('GET', '/inventory/item/1');
        $crawler = $this->client->click($crawler->selectLink('Editieren')->link());

        $this->client->click($crawler->selectLink('Löschen')->link());

        $this->assertNotRegExp('/ItemTitle0/', $this->client->getResponse()->getContent());
    }

    public function testShowListWith4Items()
    {
        $itemFixtureLoader = new ItemFixture();
        $itemFixtureLoader->load($this->entityManager, 3);

        $this->client->request('GET', '/inventory/item');
        $crawler = $this->client->followRedirect();

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /inventory/item/"
        );
        $this->assertEquals(
            4,
            $crawler->filter('table tr')->count(),
            "This List should NOT be empty"
        );
    }

    public function testNewItemWithPicture()
    {
        $crawler = $this->client->request('GET', '/inventory/item/');

        $photo = new UploadedFile(
            '/path/to/photo.jpg',
            'photo.jpg',
            'image/jpeg',
            123
        );

        $crawler = $this->client->click($crawler->selectLink('Neues Item')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Speichern')->form(
            array(
            'oktolab_bundle_rentbundle_inventory_itemtype[title]'  => 'Test',
            'oktolab_bundle_rentbundle_inventory_itemtype[description]' => 'Description',
            'oktolab_bundle_rentbundle_inventory_itemtype[barcode]' => 'ASDF01',
            'oktolab_bundle_rentbundle_inventory_itemtype[attachment][file]' => $photo
            )
        );

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        // Check data in the show view
        $this->assertGreaterThan(
            0,
            $crawler->filter('.aui-page-header-main:contains("Test")')->count(),
            'Missing element td:contains("Test")'
        );

    }
}
