<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture;

class ItemControllerTest extends WebTestCase
{

    public function testIndexDisplaysEmptyList()
    {
        $this->loadFixtures(array());

        $crawler = $this->client->request('GET', '/inventory/item/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(0, $crawler->filter('table tbody tr')->count(), 'This list has to be empty');
    }

    public function testIndexDisplaysItems()
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
            $crawler->filter('table tbody tr')->count(),
            'This list should contain at least 1 item'
        );
    }

    public function testSubmitFormToCreateAnItem()
    {
        $this->loadFixtures(array());

        $this->client->request('GET', '/inventory/item/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]'       => 'Test',
                'oktolab_bundle_rentbundle_inventory_itemtype[description]' => 'Description',
                'oktolab_bundle_rentbundle_inventory_itemtype[barcode]'     => 'ASDF01',
            )
        );

        $this->client->submit($form);
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
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture'));

        $crawler = $this->client->request('GET', '/inventory/item/1');
        $crawler = $this->client->click($crawler->selectLink('Editieren')->link());

        $this->client->request('GET', '/inventory/item/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');


        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
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

    public function testEditItemThrowsErrorOnInvalidFormData()
    {
        $itemFixtureLoader = new ItemFixture();
        $itemFixtureLoader->load($this->entityManager);

        $crawler = $this->client->request('GET', '/inventory/item/1');
        $crawler = $this->client->click($crawler->selectLink('Editieren')->link());

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]' => ''
            )
        );

        $crawler = $this->client->submit($form);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(1, $crawler->filter('.error:contains("Du musst einen Titel angeben")')->count());
    }

    public function testDeleteAnItem()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture'));


        $crawler = $this->client->request('GET', '/inventory/item/1');
        $crawler = $this->client->click($crawler->selectLink('Editieren')->link());

        $this->client->click($crawler->selectLink('Löschen')->link());

        $this->assertNotRegExp('/ItemTitle0/', $this->client->getResponse()->getContent());

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(0, $crawler->filter('table tbody tr')->count(), 'This list has to be empty');
    }

    public function testShowInvalidItemReturns404()
    {
        $this->loadFixtures(array());

        $this->client->request('GET', '/inventory/item/1');
        $this->assertTrue($this->client->getResponse()->isNotFound(), 'Response should return 404');
    }

    public function testEditInvalidItemReturns404()
    {
        $this->loadFixtures(array());
        $this->client->request('GET', '/inventory/item/1/edit');
        $this->assertTrue($this->client->getResponse()->isNotFound(), 'Response should return 404');
    }

    public function testDeleteInvalidItemReturns404()
    {
        $this->loadFixtures(array());

        $this->client->request('GET', '/inventory/item/1/delete');
        $this->assertTrue($this->client->getResponse()->isNotFound(), 'Response should return 404');
    }

    public function testNewItemWithAttachments()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
