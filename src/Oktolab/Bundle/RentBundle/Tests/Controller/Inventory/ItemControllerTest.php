<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ItemControllerTest extends WebTestCase
{

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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $place = $em->getRepository('OktolabRentBundle:Inventory\Place')->findOneBy(array('title' => 'Testplace'));

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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $place = $em->getRepository('OktolabRentBundle:Inventory\Place')->findOneBy(array('title' => 'Testplace'));

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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $place = $em->getRepository('OktolabRentBundle:Inventory\Place')->findOneBy(array('title' => 'Testplace'));

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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $item = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'ITEM0'));

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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $item = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'ITEM0'));

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

        $this->client->request('GET', '/inventory/item/'.$item->getId());
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $this->client->click($this->client->getCrawler()->selectLink('Entfernen')->link());
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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $item = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => '123456'));
        $item2 = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'QWERT'));

        $this->client->request('GET', '/inventory/item/'.$item->getId());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertRegExp('/27.10.1991/', $this->client->getCrawler()->filter('#inventory-item-buyDate')->text());

        $this->client->request('GET', '/inventory/item/'.$item2->getId());
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertEmpty($this->client->getCrawler()->filter('#inventory-item-buyDate')->text());
    }
}
