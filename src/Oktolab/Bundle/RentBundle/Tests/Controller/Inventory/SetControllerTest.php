<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SetControllerTest extends WebTestCase
{

    public function testIndexDisplaysList()
    {
        $this->loadFixtures(array());

        $crawler = $this->client->request('GET', '/inventory/set/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(
            0,
            $crawler->filter('#content table tbody tr')->count(),
            'There should be no sets in this list'
        );
    }

    public function testIndexDisplaysSets()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\SetFixture'));

        $crawler = $this->client->request('GET', '/inventory/set/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertGreaterThan(
            0,
            $crawler->filter('#content table tbody tr')->count(),
            'This list should contain at least 1 set'
        );
    }

    public function testSubmitFormToCreateASet()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\PlaceFixture'));

        $crawler = $this->client->request('GET', '/inventory/set/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');


        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_rentbundle_inventory_set[title]'       => 'TestSet',
                'oktolab_rentbundle_inventory_set[description]' => 'TestDescription',
                'oktolab_rentbundle_inventory_set[barcode]'     => 'ASDF0',
                'oktolab_rentbundle_inventory_set[place]'       => 1
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
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\SetFixture'));

        $this->client->request('GET', '/inventory/set/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_rentbundle_inventory_set[title]'  => 'Foo'
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
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\SetFixture'));
        $crawler = $this->client->request('GET', '/inventory/set/1/edit');

        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_rentbundle_inventory_set[title]' => '',
            )
        );

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(1, $crawler->filter('html:contains("Du musst einen Titel angeben")')->count());
    }

    public function testDeleteASet()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\SetFixture'));

        $this->client->request('GET', '/inventory/set/1/delete');
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/inventory/set/'),
            'Response should be a redirect to Set index'
        );

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(0, $crawler->filter('#content table tbody tr')->count(), 'This list has to be empty');
    }

    public function testAddItemToSet()
    {
        $this->markTestIncomplete('Refactoring of Views/Controllers is WIP.');
        //only possible with javascript. we use a modified Form and post it.
        $this->loadFixtures(
            array(
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\SetFixture',
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ItemFixture',
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\PlaceFixture'
            )
        );

        $this->client->request('GET', '/inventory/set/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form();
        $this->client->request(
            'PUT',
            $form->getUri(),
            array(
                'oktolab_rentbundle_inventory_set' => array(
                    '_token'      => $form['oktolab_rentbundle_inventory_set[_token]']->getValue(),
                    'title'       => 'TestSet',
                    'description' => 'TestDescription',
                    'barcode'     => 'ASDF0',
                    'items'       => array(0 => '1'),
                    'place'       => 1
                )
            )
        );
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/inventory/set/1'),
            'Response should be a redirect to Set'
        );

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $this->assertEquals(1, $this->client->getCrawler()->filter('#content tbody tr')->count());
    }

    public function testRemoveItemFromSet()
    {
        $this->loadFixtures(
            array(
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\SetFixture',
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ItemFixture',
            )
        );

        $this->client->request('GET', '/inventory/set/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $form = $this->client->getCrawler()->selectButton('Speichern')->form();
        $crawler = $this->client->request(
            'PUT',
            $form->getUri(),
            array(
                'oktolab_rentbundle_inventory_set' => array(
                    '_token'        => $form['oktolab_rentbundle_inventory_set[_token]']->getValue(),
                    'title'         => 'SetWithoutItem',
                    'description'   => 'SetWithoutItemDescription',
                    'barcode'       => 'ASDF0',
                    'place'         => 1
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
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\SetWithItemFixture'));

        $this->client->request('GET', '/inventory/set/1/delete');
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/inventory/set/'),
            'Expected to be redirected to Set index'
        );

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(
            0,
            $this->client->getCrawler()->filter('#content table:contains("SetWithItemTitle")')->count(),
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

        $this->client->request('GET', '/inventory/set/1/edit');
        $this->assertTrue($this->client->getResponse()->isNotFound(), 'Response should return 404');
    }

    public function testItemsSearchReturnsJson()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ItemFixture'));

        $this->client->request(
            'GET',
            '/api/item/typeahead.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'HTTP_X-Requested-With' => 'XMLHttpRequest')
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response should be successful');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Returns application/json');
        $this->assertNotNull(json_decode($response->getContent()), 'Should be able to decode JSON');
    }

    public function testNewSetWithAttachment()
    {
        $this->markTestIncomplete('Refactoring of Views/Controllers is WIP.');
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\PlaceFixture'));

        copy(__DIR__.'/../../DataFixtures/logo_okto.png', $filename = tempnam(sys_get_temp_dir(), 'OktolabRentBundle'));
        $file = new UploadedFile($filename, basename($filename), 'image/png', filesize($filename), null, true);

        $this->client->request(
            'POST',
            '/_uploader/gallery/upload',
            array(
                'Content-Length' => $file->getSize(),
                'Content-Type' => 'multipart/form-data',
            ),
            array($file)
        );

        $this->client->request('GET', '/inventory/set/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_rentbundle_inventory_set[title]'       => 'Test',
                'oktolab_rentbundle_inventory_set[description]' => 'Description',
                'oktolab_rentbundle_inventory_set[barcode]'     => 'ASDF01',
                'oktolab_rentbundle_inventory_set[place]'       => 1

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
        $this->assertEquals(1, $crawler->filter('.aui-expander-content > img')->count(), 'Contains no Attachment');
    }
}
