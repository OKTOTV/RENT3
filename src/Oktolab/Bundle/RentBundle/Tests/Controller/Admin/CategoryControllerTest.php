<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Admin;

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
            $crawler->filter('.aui-page-panel-content:contains("Testcategory")')->count(),
            'The Category title should appear on this page.'
        );
    }

    public function testSubmitFormToUpdateACategory()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CategoryFixture'));

        // load page
        $this->client->request('GET', '/admin/inventory/category/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        // fill form and submit it
        $this->client->submit(
            $this->client->getCrawler()->selectButton('Speichern')->form(),
            array('oktolab_bundle_rentbundle_inventory_categorytype[title]' => 'Changed Category Name')
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect');

        // check redirect and page content
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(
            1,
            $crawler->filter('.aui-page-panel-content:contains("Changed Category Name")')->count(),
            'The new Category title should appear on this page.'
        );
    }
    
}
