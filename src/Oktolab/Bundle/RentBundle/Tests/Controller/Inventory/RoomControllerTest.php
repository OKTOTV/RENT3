<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

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

    public function testNewRoomWithAttachments()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
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
