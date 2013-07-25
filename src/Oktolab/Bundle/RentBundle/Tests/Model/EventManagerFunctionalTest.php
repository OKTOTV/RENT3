<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class EventManagerFunctionalTest extends WebTestCase
{

    public function testObjectIsAvailable()
    {
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\EventFixture'));

        // testcase: object 14:00 -> 15:00, db-event 13:00 -> 15:00
        //$this->assertTrue()

        // testcase: object 14:00 -> 15:00, db-event 14:00 -> 14:30

        // testcase: object 14:00 -> 15:00, db-event 15:00 -> 16:00

        // testcase: object 14:00 -> 15:00, db-event 14:10 -> 14:50
    }

//    public function testIndexDisplaysEmptyList()
//    {
//        $this->loadFixtures(array());
//
//        $crawler = $this->client->request('GET', '/inventory/item/');
//        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
//        $this->assertEquals(0, $crawler->filter('table tbody tr')->count(), 'This list has to be empty');
//    }
//
//    public function testIndexDisplaysItems()
//    {
//        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\DataFixtures\ORM\ItemFixture'));
//
//        $crawler = $this->client->request('GET', '/inventory/item/');
//        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
//        $this->assertGreaterThan(
//            0,
//            $crawler->filter('table tbody tr')->count(),
//            'This list should contain at least 1 item'
//        );
//    }
}
