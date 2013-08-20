<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportControllerTest extends WebTestCase
{
    public function testImportCsvWithItems()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array());

        $file = new UploadedFile(__DIR__.'/../../DataFixtures/items.csv', 'items.csv', 'text/csv', 399);

        $this->client->request('GET', '/admin/inventory/import/');
        $form = $this->client->getCrawler()->selectButton('Upload')->form();
        $form['form[csv]'] = $file;
        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(4, $crawler->filter('section.aui-page-panel-content table.aui tbody tr')->count(), 'Contains no Item');

//        die(var_dump($this->client->getCrawler()->selectButton('Speichern')->form()));
        $this->client->submit($this->client->getCrawler()->selectButton('Speichern')->form());
        echo $this->client->getResponse()->getContent(); die();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect');
        $crawler = $this->client->followRedirect();

        $this->assertEquals(4, $crawler->filter('section.aui-page-panel-content table.aui tbody tr')->count());
    }
}