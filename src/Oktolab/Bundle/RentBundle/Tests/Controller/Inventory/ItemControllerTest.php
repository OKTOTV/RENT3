<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ItemControllerTest extends WebTestCase
{
    private $em = null;

    private function getEm()
    {
        if ($this->em == null) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        }
        return $this->em;
    }

    public function testIndexDisplaysEmptyList()
    {
        $this->loadFixtures(array('\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'));

        $crawler = $this->client->request('GET', '/inventory/items');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(0, $crawler->filter('#content table tbody tr')->count(), 'This list has to be empty');
    }

    public function testSubmitFormToCreateAnItemWithOptionalValue()
    {
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\PlaceFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));
        $place = $this->getEm()->getRepository('OktolabRentBundle:Inventory\Place')
              ->findOneBy(array('title' => 'Testplace'));

        $this->client->request('GET', '/inventory/item/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]'       => 'Test',
                'oktolab_bundle_rentbundle_inventory_itemtype[description]' => 'Description',
                'oktolab_bundle_rentbundle_inventory_itemtype[barcode]'     => 'ASDF01',
                'oktolab_bundle_rentbundle_inventory_itemtype[place]'       => $place->getId(),
                'oktolab_bundle_rentbundle_inventory_itemtype[origin_value]' => 23.4
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
        $this->assertEquals(1, $crawler->filter('section.aui-page-panel-content:contains("23.4")')->count());
    }

    public function testSubmitFormToCreateAnItemWithOptionalRent()
    {
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\PlaceFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));
        $place = $this->getEm()->getRepository('OktolabRentBundle:Inventory\Place')
              ->findOneBy(array('title' => 'Testplace'));

        $this->client->request('GET', '/inventory/item/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]'       => 'Test',
                'oktolab_bundle_rentbundle_inventory_itemtype[description]' => 'Description',
                'oktolab_bundle_rentbundle_inventory_itemtype[barcode]'     => 'ASDF01',
                'oktolab_bundle_rentbundle_inventory_itemtype[place]'       => $place->getId(),
                'oktolab_bundle_rentbundle_inventory_itemtype[daily_rent]' => 22.2
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
        $this->assertEquals(1, $crawler->filter('section.aui-page-panel-content:contains("22.2")')->count());
    }

        public function testSubmitFormToCreateAnItemWithOptionalNotice()
    {
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\PlaceFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));
        $place = $this->getEm()->getRepository('OktolabRentBundle:Inventory\Place')
              ->findOneBy(array('title' => 'Testplace'));

        $this->client->request('GET', '/inventory/item/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]'       => 'Test',
                'oktolab_bundle_rentbundle_inventory_itemtype[description]' => 'Description',
                'oktolab_bundle_rentbundle_inventory_itemtype[barcode]'     => 'ASDF01',
                'oktolab_bundle_rentbundle_inventory_itemtype[place]'       => $place->getId(),
                'oktolab_bundle_rentbundle_inventory_itemtype[notice]'      => 'Cool stuff'
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
        $this->assertEquals(1, $crawler->filter('section.aui-page-panel-content:contains("Cool stuff")')->count());
    }

    public function testSubmitFormToEditAnItem()
    {
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ItemFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));
        $item = $this->getEm()->getRepository('OktolabRentBundle:Inventory\Item')
              ->findOneBy(array('barcode' => 'ITEM0'));

        $crawler = $this->client->request('GET', '/inventory/item/'.$item->getId());
        $crawler = $this->client->click($crawler->selectLink('Bearbeiten')->link());

        $this->client->request('GET', '/inventory/item/'.$item->getId().'/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');


        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]'  => 'Foo',
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

    public function testEditItemThrowsErrorOnInvalidFormData()
    {
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ItemFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));
        $item = $this->getEm()->getRepository('OktolabRentBundle:Inventory\Item')
              ->findOneBy(array('barcode' => 'ITEM0'));

        $this->client->request('GET', '/inventory/item/'.$item->getId().'/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]' => ''
            )
        );

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(1, $crawler->filter('.error:contains("Du musst einen Titel angeben")')->count());
    }

    public function testDeleteAnItem()
    {
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ItemFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $item = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'ITEM0'));

        $this->client->request('GET', '/inventory/item/'.$item->getId().'/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $this->client->click($this->client->getCrawler()->selectLink('Löschen')->link());
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response is redirect.');

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(0, $crawler->filter('#content table tbody tr')->count(), 'This list has to be empty');

        $this->client->request('GET', '/inventory/item/'.$item->getId());
        $this->assertTrue($this->client->getResponse()->isNotFound(), 'Response is not found.');
    }

    public function testShowInvalidItemReturns404()
    {
        $this->loadFixtures(array('\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'));

        $this->client->request('GET', '/inventory/item/1');
        $this->assertTrue($this->client->getResponse()->isNotFound(), 'Response should return 404');
    }

    public function testEditInvalidItemReturns404()
    {
        $this->loadFixtures(array('\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'));
        $this->client->request('GET', '/inventory/item/1/edit');
        $this->assertTrue($this->client->getResponse()->isNotFound(), 'Response should return 404');
    }

    public function testDeleteInvalidItemReturns404()
    {
        $this->loadFixtures(array('\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'));

        $this->client->request('GET', '/inventory/item/1/delete');
        $this->assertTrue($this->client->getResponse()->isNotFound(), 'Response should return 404');
    }

    public function testNewItemWithAttachments()
    {
        $this->markTestIncomplete('Attachments should not be uploaded while creating new item.');
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\PlaceFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));

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

        $this->client->request('GET', '/inventory/item/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_itemtype[title]'       => 'Test',
                'oktolab_bundle_rentbundle_inventory_itemtype[description]' => 'Description',
                'oktolab_bundle_rentbundle_inventory_itemtype[barcode]'     => 'ASDF01',
                'oktolab_bundle_rentbundle_inventory_itemtype[place]'       => 1
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

    /**
     * @ticket http://jira.okto.tv/browse/RENT-132
     */
    public function testBuyDateIsEmtyWhenEmpty()
    {
        $this->loadFixtures(array(
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ItemBuyDateFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));
        $item = $this->getEm()->getRepository('OktolabRentBundle:Inventory\Item')
              ->findOneBy(array('barcode' => '123456'));
        $item2 = $this->getEm()->getRepository('OktolabRentBundle:Inventory\Item')
              ->findOneBy(array('barcode' => 'QWERT'));

        $this->client->request('GET', '/inventory/item/'.$item->getId());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertRegExp('/27.10.1991/', $this->client->getCrawler()->filter('#inventory-item-buyDate')->text());

        $this->client->request('GET', '/inventory/item/'.$item2->getId());
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertEmpty($this->client->getCrawler()->filter('#inventory-item-buyDate')->text());
    }

    /**
     * @test
     */
    public function testQmsHistory()
    {
        $this->loadFixtures(array(
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ItemQmsFixture'
        ));
        $item = $this->getEm()->getRepository('OktolabRentBundle:Inventory\Item')
              ->findOneBy(array('barcode' => 'QMS001'));
        $qmss = $item->getQmss();

        $crawler = $this->client->request('GET', '/inventory/item/'.$item->getId());
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $qmsTable = $crawler->filter('body table tbody');

        $this->assertEquals(1, count($qmsTable->filter('tr')), 'There should be one qms in this table.');
        $this->assertEquals(4, count($qmsTable->filter('tr td')), 'There should be four table data per row.');

        $this->assertEquals(1, count($qmsTable->filter('tr > td:contains("'.$qmss[0]->getCreatedAt()->format('d.m.Y').'")')), 'There should be a DateTime.');
        $this->assertEquals(1, count($qmsTable->filter('tr > td:contains("random description")')), 'There should be a QmsDescription.');
        $this->assertEquals(1, count($qmsTable->filter('tr > td:contains("In Ordnung")')), 'The Qms State should be STATE_OKAY.');
        $this->assertEquals(1, count($qmsTable->filter('tr > td:contains("Interner Status")')), 'The Qms Cost Unit should be Interner Status.');
    }

    /**
     * @test
     */
    public function testInactiveIndexEmpty()
    {
        $this->loadFixtures(array('\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'));
        $crawler = $this->client->request('GET', '/inventory/items/inactive');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(1, count($crawler->filter('body:contains("Keine inaktiven Gegenstände vorhanden")')));

        $this->loadFixtures(array());
    }

    /**
     * @test
     */
    public function testInactiveIndex()
    {
        $this->loadFixtures(array(
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ItemInactiveFixture'
            )
        );

        $crawler = $this->client->request('GET', '/inventory/items/inactive');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(0, count($crawler->filter('body:contains("Keine inaktiven Gegenstände vorhanden")')));
    }
}
