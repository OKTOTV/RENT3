<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RoomControllerTest extends WebTestCase
{
    public function testIndexDisplaysEmptyList()
    {
        $this->loadFixtures(array());

        $crawler = $this->client->request('GET', '/inventory/room/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(0, $crawler->filter('#content table tbody tr')->count(), 'This list has to be empty');
    }

    public function testCreateNewRoom()
    {
        $crawler = $this->client->request('GET', '/inventory/room/new');
        $form = $crawler->selectButton('Speichern')->form(
            array(
            'oktolab_bundle_rentbundle_inventory_roomtype[title]'  => 'Test',
            'oktolab_bundle_rentbundle_inventory_roomtype[description]' => 'Description',
            'oktolab_bundle_rentbundle_inventory_roomtype[barcode]' => 'ASDF01'
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

    public function testSubmitFormToEditARoom()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\RoomFixture'));

        $crawler = $this->client->request('GET', '/inventory/room/1');
        $crawler = $this->client->click($crawler->selectLink('Bearbeiten')->link());

        $this->client->request('GET', '/inventory/room/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');


        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_roomtype[title]'  => 'Foo',
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

    public function testDeleteRoom()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\RoomFixture'));


        $crawler = $this->client->request('GET', '/inventory/room/1');
        $crawler = $this->client->click($crawler->selectLink('Bearbeiten')->link());

        $this->client->click($crawler->selectLink('Löschen')->link());

        $this->assertNotRegExp('/RoomTitle0/', $this->client->getResponse()->getContent());

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(0, $crawler->filter('#content table tbody tr')->count(), 'This list has to be empty');
    }

    public function testEditRoomThrowsErrorOnInvalidFormData()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\RoomFixture'));

        $this->client->request('GET', '/inventory/room/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_roomtype[title]' => ''
            )
        );

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(1, $crawler->filter('.error:contains("Du musst einen Titel angeben")')->count());
    }

    public function testNewSetWithAttachment()
    {
        $this->loadFixtures(array());

        $this->uploadTestFile();

        $this->client->request('GET', '/inventory/room/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_roomtype[title]'       => 'Test',
                'oktolab_bundle_rentbundle_inventory_roomtype[description]' => 'Description',
                'oktolab_bundle_rentbundle_inventory_roomtype[barcode]'     => 'ASDF01',
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

    public function testDeleteRoomWithAttachments()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\RoomFixture'));

        $this->uploadTestFile();

        $crawler = $this->client->request('GET', '/inventory/room/1');
        $crawler = $this->client->click($crawler->selectLink('Bearbeiten')->link());

        $this->client->request('GET', '/inventory/room/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');


        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_roomtype[title]'  => 'Foo',
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirection(), 'Response should be a redirection');
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(1, $crawler->filter('.aui-expander-content > img')->count(), 'Contains no Attachment');

        $attachment = $this->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('OktolabRentBundle:Inventory\Attachment')
            ->findOneBy(array('id' => 1));

        $uploadpath = $this->getContainer()
            ->getParameter('oktolab.web_dir').$this->getContainer()->getParameter('oktolab.upload_dir');

        $this->assertTrue(file_exists($uploadpath.$attachment->getPath().'/'.$attachment->getTitle()));

        $this->client->request('GET', '/inventory/room/1/delete');
        $this->assertFalse(file_exists($uploadpath.$attachment->getPath().'/'.$attachment->getTitle()));
    }

    private function uploadTestFile()
    {
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
    }
}
