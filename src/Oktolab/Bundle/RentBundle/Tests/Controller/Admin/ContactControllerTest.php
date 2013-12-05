<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Admin;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testListAllContacts()
    {
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ContactFixture',
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));
        $this->logIn('ROLE_ADMIN');

        $this->client->request('GET', '/admin/contact/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(1, $this->client->getCrawler()->filter('#content tbody tr')->count());
    }

    public function testShowContact()
    {
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ContactFixture',
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));
        $this->logIn('ROLE_ADMIN');

        $crawler = $this->client->request('GET', '/admin/contact/1');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(1, $crawler->filter('.aui-page-panel-content:contains("John Appleseed")')->count());
    }
}
