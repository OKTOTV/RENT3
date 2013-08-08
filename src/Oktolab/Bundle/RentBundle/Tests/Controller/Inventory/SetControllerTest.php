<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class SetControllerTest extends WebTestCase
{

    public function testIndexDisplaysList()
    {
        $this->loadFixtures(array());

        $crawler = $this->client->request('GET', '/inventory/set/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(0, $crawler->filter('table tbody tr')->count(), 'There should be no sets in this list');
    }

    public function testIndexDisplaysSets()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\SetFixture'));

        $crawler = $this->client->request('GET', '/inventory/set/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertGreaterThan(
            0,
            $crawler->filter('table tbody tr')->count(),
            'This list should contain at least 1 set'

        );
    }

    public function testSubmitFormToCreateASet()
    {
        $this->loadFixtures(array());

        $crawler = $this->client->request('GET', '/inventory/set/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
            'oktolab_bundle_rentbundle_inventory_settype[title]' => 'TestSet',
            'oktolab_bundle_rentbundle_inventory_settype[description]' => 'TestDescription',
            'oktolab_bundle_rentbundle_inventory_settype[barcode]' => 'ASDF0'
            )
        );

        $crawler = $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(
            1,
            $crawler->filter('.aui-page-header-main:contains("TestSet")')->count(),
            'There should be the set name on this page header'
        );
    }

    public function testSubmitFormToEditASet()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\SetFixture'));

        $this->client->request('GET', '/inventory/set/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_settype[title]'  => 'Foo'
            )
        );

        $this->client->submit($form);
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/inventory/set/1'),
            'Response should be a redirect to Set'
        );
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(
            1,
            $crawler->filter('.aui-page-header-main:contains("Foo")')->count(),
            'The page header should contain the new name [value="Foo"]'
        );
    }

    public function testSubmitFormForEditWithInvalidDataThrowsError()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\SetFixture'));
        $crawler = $this->client->request('GET', '/inventory/set/1/edit');

        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_settype[title]' => ''
            )
        );

        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("Du musst einen Titel angeben")')->count());
    }

    public function testDeleteASet()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\SetFixture'));

        $this->client->request('GET', '/inventory/set/1/delete');
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/inventory/set/'),
            'Response should be a redirect to Set index'
        );

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(0, $crawler->filter('table tbody tr')->count(), 'This list has to be empty');
    }

    public function testAddItemToSet()
    {
        $setFixtureLoader = new SetFixture();
        $setFixtureLoader->load($this->entityManager);

        $itemFixtureLoader = new ItemFixture();
        $itemFixtureLoader->load($this->entityManager);

        $crawler = $this->client->request('GET', '/inventory/set/1/edit');

        $form = $crawler->selectButton('Speichern')->form();
        //only possible with javascript. we use a modified Form and post it.
        $this->loadFixtures(
            array(
                'Oktolab\Bundle\RentBundle\DataFixtures\ORM\SetFixture',
                'Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture',
            )
        );

        $this->client->request('GET', '/inventory/set/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form();
        $this->client->request(
            'PUT',
            $form->getUri(),
            array(
                'oktolab_bundle_rentbundle_inventory_settype' => array(
                    '_token'      => $form['oktolab_bundle_rentbundle_inventory_settype[_token]']->getValue(),
                    'title'       => 'TestSet',
                    'description' => 'TestDescription',
                    'barcode'     => 'ASDF0',
                    'itemsToAdd'  => array(1 => 'id'),
                )
            )
        );
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/inventory/set/1'),
            'Response should be a redirect to Set'
        );

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $this->assertEquals(1, $this->client->getCrawler()->filter('tbody tr')->count());
    }

    public function testRemoveItemFromSet()
    {
        $this->loadFixtures(
            array(
                'Oktolab\Bundle\RentBundle\DataFixtures\ORM\SetFixture',
                'Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture',
            )
        );

        $this->client->request('GET', '/inventory/set/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $crawler = $this->client->request(
            'PUT',
            $form->getUri(),
            array(
                'oktolab_bundle_rentbundle_inventory_settype' => array(
                    '_token'        => $form['oktolab_bundle_rentbundle_inventory_settype[_token]']->getValue(),
                    'title'         => 'SetWithoutItem',
                    'description'   => 'SetWithoutItemDescription',
                    'barcode'       => 'ASDF0',
                )
            )
        );
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/inventory/set/1'),
            'Response should be a redirect to Set'
        );

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
    }

    public function testDeleteSetWithAttachedItem()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\SetWithItemFixture'));

        $this->client->request('GET', '/inventory/set/1/delete');
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/inventory/set/'),
            'Expected to be redirected to Set index'
        );

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(
            0,
            $this->client->getCrawler()->filter('table:contains("SetWithItemTitle")')->count(),
            'Set should be deleted'
        );

        $this->client->request('GET', '/inventory/item/1');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(
            1,
            $this->client->getCrawler()->filter('header.aui-page-header:contains("SharedItem")')->count(),
            'Page Header should contain Item title "SharedItem"'
        );
    }

    public function testShowInvalidSetReturns404()
    {
        $this->loadFixtures(array());

        $this->client->request('GET', '/inventory/set/1');
        $this->assertTrue($this->client->getResponse()->isNotFound(), 'Response should return 404');
    }

    public function testDeleteInvalidSetReturns404()
    {
        $this->loadFixtures(array());

        $crawler = $this->client->request('GET', '/inventory/set/1/edit');
        $this->client->click($crawler->selectLink('Löschen')->link());

        $this->assertNotRegExp('/setWithItemTitle/', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/inventory/set/1/delete');
        $this->assertTrue($this->client->getResponse()->isNotFound(), 'Response should return 404');
    }

    public function testItemsSearchReturnsJson()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture'));

        $this->client->request(
            'GET',
            '/inventory/set/search.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'HTTP_X-Requested-With' => 'XMLHttpRequest')
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response should be successful');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertNotNull(json_decode($response->getContent()), 'Should be able to decode JSON');
    }
}
