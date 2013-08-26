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
}
