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

        $this->client->request('GET', '/inventory/set/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
            'oktolab_bundle_rentbundle_inventory_settype[title]' => 'TestSet',
            'oktolab_bundle_rentbundle_inventory_settype[description]' => 'TestDescription',
            'oktolab_bundle_rentbundle_inventory_settype[barcode]' => 'ASDF0'
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

        $this->client->request('GET', '/inventory/set/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_settype[title]' => ''
            )
        );

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
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

        $form = $this->client->getCrawler()->selectButton('Speichern')->form();
        $this->client->request(
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
}
