<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    public function testSubmitFormToCreateNewCategory()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array());

        $this->client->request('GET', '/admin/inventory/category/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_categorytype[title]' => 'Testcategory',
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect');

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(
            1,
            $crawler->filter('.aui-page-header-main:contains("Testcategory")')->count(),
            'There should be the place name on this page header'
        );
    }
}
