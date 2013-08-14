<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    public function testSubmitFormToCreateNewPlace()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array());

        $crawler = $this->client->request('GET', '/admin/inventory/category/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_categorytype[title]' => 'Testplace'
            )
        );

        $crawler = $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(
            1,
            $crawler->filter('.aui-page-header-main:contains("Testplace")')->count(),
            'There should be the place name on this page header'
        );
    }
}