<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportControllerTest extends WebTestCase
{
    public function testImportCsvWithItems()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(
            array(
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\PlaceFixture',
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CategoryFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
            )
        );

        $file = new UploadedFile(__DIR__.'/../../DataFixtures/items.csv', 'items.csv', 'text/csv', 399);

        $this->client->request('GET', '/admin/inventory/import/');
        $form = $this->client->getCrawler()->selectButton('Hochladen')->form();
        $form['form[csv]'] = $file;
        $crawler = $this->client->submit($form);
        
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Form submit was not successful');
        $this->assertEquals(
            4,
            $crawler->filter('section.aui-page-panel-content table.aui tbody tr')->count(),
            'Contains no Item'
        );

        $this->client->submit($this->client->getCrawler()->selectButton('Speichern')->form());
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect');

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');
        $this->assertEquals(4, $crawler->filter('section.aui-page-panel-content table.aui tbody tr')->count());

        $text = $crawler->filter('section.aui-page-panel-content table.aui tbody')->text();
        $this->assertEquals(2, substr_count($text, 'Camera'));
    }
}
