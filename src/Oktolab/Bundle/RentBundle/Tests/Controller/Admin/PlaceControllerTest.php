<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Admin;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class PlaceControllerTest extends WebTestCase
{
    public function testSubmitFormToCreateNewPlace()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array());

        $this->client->request('GET', '/admin/inventory/place/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_placetype[title]' => 'Testplace',
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect');

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(
            1,
            $crawler->filter('.aui-page-panel-content:contains("Testplace")')->count(),
            'The Place title should appear on this page.'
        );
    }

    public function testSubmitFormToUpdateAPlace()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\PlaceFixture'));

        // load page
        $this->client->request('GET', '/admin/inventory/place/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        // fill and submit form
        $this->client->submit(
            $this->client->getCrawler()->selectButton('Speichern')->form(),
            array('oktolab_bundle_rentbundle_inventory_placetype[title]' => 'Changed Place Title')
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirection.');

        // follow redirection and check values
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $this->assertEquals(
            1,
            $crawler->filter('.aui-page-panel-content:contains("Changed Place Title")')->count(),
            'The new Place title should appear on this page.'
        );
    }

    public function testDeletePlaceWithItemShouldFail()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture'));

        // load page
        $this->client->request('GET', '/admin/inventory/place/1/delete');
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirection.');

        // follow redirection and check values
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $this->assertEquals(
            1,
            $crawler->filter('.aui-message.error:contains("kann nicht gelöscht werden,")')->count(),
            'An AUI Error-Message should appear on this page.'
        );
    }
}
