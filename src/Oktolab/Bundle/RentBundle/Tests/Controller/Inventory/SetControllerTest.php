<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Inventory;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Oktolab\Bundle\RentBundle\DataFixtures\ORM\SetFixture;
use Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class SetControllerTest extends WebTestCase
{
    private $entityManager;
    private $purger;

    public function setUp()
    {
        parent::setUp();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->purger = new ORMPurger($this->entityManager);
        $this->purger->purge();
    }

    public function testShowEmptySetList()
    {
        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/set');
        $client->followRedirect();
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET inventory/set/"
        );

        $this->assertEquals(
            0,
            $crawler->filter('table tr')->count(),
            "There should be no sets in this list"
        );
    }

    public function testCreateSet()
    {
        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/set/new');

        $form = $crawler->selectButton('Speichern')->form(
            array(
            'oktolab_bundle_rentbundle_inventory_settype[title]' => 'TestSet',
            'oktolab_bundle_rentbundle_inventory_settype[description]' => 'TestDescription'
            )
        );

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('.aui-page-header-main:contains("TestSet")')->count(),
            'There should be the set name on this page header'
        );
    }

    public function testEditSet()
    {
        $setFixtureLoader = new SetFixture();
        $setFixtureLoader->load($this->entityManager);

        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/set/1/edit');

        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_settype[title]'  => 'Foo'
            )
        );

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('.aui-page-header-main:contains("Foo")')->count(),
            'The page header should contain the new name [value="Foo"]'
        );
    }

    public function testEditErrorSet()
    {
        $setFixtureLoader = new SetFixture();
        $setFixtureLoader->load($this->entityManager);

        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/set/1/edit');

        $form = $crawler->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_inventory_settype[title]' => ''
            )
        );

        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("Du musst einen Titel angeben")')->count());
    }

    public function testDeleteSet()
    {
        $setFixtureLoader = new SetFixture();
        $setFixtureLoader->load($this->entityManager);

        $client = $this->client;
        // Delete the entity,
        $crawler = $client->request('GET', 'inventory/set/1/edit');

        $client->click($crawler->selectLink('Löschen')->link());

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }

    public function testAddItemToSet()
    {
        $setFixtureLoader = new SetFixture();
        $setFixtureLoader->load($this->entityManager);

        $itemFixtureLoader = new ItemFixture();
        $itemFixtureLoader->load($this->entityManager);

        //only possible with javascript. we use a modified Form and post it.
        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/set/1/edit');

        $form = $crawler->selectButton('Speichern')->form();

        $crawler = $client->request(
            'PUT',
            $form->getUri(),
            array(
                'oktolab_bundle_rentbundle_inventory_settype' => array(
                    '_token' => $form['oktolab_bundle_rentbundle_inventory_settype[_token]']->getValue(),
                    'title' => 'TestSet',
                    'description' => 'TestDescription',
                    'itemsToAdd' => array(1 => 'id')
                )
            )
        );

        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('tbody tr')->count());

    }

    public function testRemoveItemFromSet()
    {
        $setFixtureLoader = new SetFixture();
        $setFixtureLoader->setWithItem($this->entityManager);

        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/set/1/edit');

        $form = $crawler->selectButton('Speichern')->form();

        $crawler = $client->request(
            'PUT',
            $form->getUri(),
            array(
                'oktolab_bundle_rentbundle_inventory_settype' => array(
                    '_token' => $form['oktolab_bundle_rentbundle_inventory_settype[_token]']->getValue(),
                    'title' => 'SetWithoutItem',
                    'description' => 'SetWithoutItemDescription'
                    )
            )
        );

        $crawler = $client->followRedirect();

        $this->assertEquals(0, $crawler->filter('tbody tr')->count());
    }

    public function testDeleteSetWithAttachedItems()
    {
        $setFixtureLoader = new SetFixture();
        $setFixtureLoader->setWithItem($this->entityManager);

        $client = $this->client;

        $crawler = $client->request('GET', 'inventory/set/1/edit');
        $client->click($crawler->selectLink('Löschen')->link());

        $this->assertNotRegExp('/setWithItemTitle/', $client->getResponse()->getContent());
    }
}
