<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RoomControllerTest extends WebTestCase
{
    public function testListAllRooms()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
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

    public function testEditRoom()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testUpdateRoom()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testDeleteRoom()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testEditRoomThrowsErrorOnInvalidFormData()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testNewSetWithAttachment()
    {
        $this->loadFixtures(array());

        // post new attachment
        copy(__DIR__.'/../../DataFixtures/logo_okto.png', $filename = tempnam(sys_get_temp_dir(), 'OktolabRentBundle'));
        $file = new UploadedFile($filename, basename($filename), 'image/png', filesize($filename), null, true);

        $this->client->request(
            'POST',
            '/_uploader/gallery/upload',
            array(
                'Content-Length' => $file->getSize(),
                'Content-Type' => 'multipart/form-data',
            ),
            array($file));
        // - - - - - - - - - -


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

    public function testUpdateRoomWithAttachments()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testDeleteRoomWithAttachments()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
