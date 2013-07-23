<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class ItemControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Doctrine\Common\DataFixtures\Purger\ORMPurger;
     */
    private $purger;

    /**
     * {@inheritDoc}
     */
//    public function setUp()
//    {
//        parent::setUp();
////        $this->entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
////        $this->purger = new ORMPurger($this->entityManager);
////        $this->purger->purge();
//    }

    /**
     * {@inheritDoc}
     */
//    protected function tearDown()
//    {
//        parent::tearDown();
////        $this->entityManager->close();
//    }

    public function testViewDisplaysEmptyList()
    {
        $this->loadFixtures(array());
        $crawler = $this->client->request('GET', '/inventory/item/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(0, $crawler->filter('table tbody tr')->count(), 'This list has to be empty');
    }

    public function testSubmitFormToCreateAnItem()
    {
        $this->loadFixtures(array());
        $crawler = $this->client->request('GET', '/inventory/item/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $crawler = $this->client->click($crawler->selectLink('Neues Item')->link());
        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]'       => 'Test',
                'oktolab_bundle_rentbundle_inventory_itemtype[description]' => 'Description',
                'oktolab_bundle_rentbundle_inventory_itemtype[barcode]'     => 'ASDF01',
            )
        );

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirection(), 'Response should be a redirection');

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertGreaterThan(
            0,
            $crawler->filter('header.aui-page-header:contains("Test")')->count(),
            'Missing element td:contains("Test")'
        );
    }

    public function testSubmitFormToEditAnItem()
    {
//        $itemFixtureLoader = new ItemFixture();
//        $itemFixtureLoader->load($this->entityManager);

        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture'));

        $crawler = $this->client->request('GET', '/inventory/item/1');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $crawler = $this->client->click($crawler->selectLink('Editieren')->link());
        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]'  => 'Foo',
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirection(), 'Response should be a redirection');

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertGreaterThan(
            0,
            $crawler->filter('.aui-page-header-main:contains("Foo")')->count(),
            'Missing element [value="Foo"]'
        );
    }

    public function testEditErrorItem()
    {
//        $itemFixtureLoader = new ItemFixture();
//        $itemFixtureLoader->load($this->entityManager);

        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture'));

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
//        $itemFixtureLoader = new ItemFixture();
//        $itemFixtureLoader->load($this->entityManager);

        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture'));

        $client = $this->client;
        // Delete the entity,
        $crawler = $client->request('GET', 'inventory/item/1');
        $crawler = $client->click($crawler->selectLink('Editieren')->link());

        $client->click($crawler->selectLink('Löschen')->link());
        //$crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/ItemTitle0/', $client->getResponse()->getContent());
    }

//    public function testShowListWith4Items()
//    {
//        $itemFixtureLoader = new ItemFixture();
//        $itemFixtureLoader->load($this->entityManager, 3);
//
//        $client = $this->client;
//
//        $client->request('GET', 'inventory/item');
//        $crawler = $client->followRedirect();
//
//        $this->assertEquals(
//            200,
//            $client->getResponse()->getStatusCode(),
//            "Unexpected HTTP status code for GET /inventory/item/"
//        );
//        $this->assertEquals(
//            4,
//            $crawler->filter('table tr')->count(),
//            "This List should NOT be empty"
//        );
//    }
}
