<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response;

class ContactProviderTest extends WebTestCase
{
    public function testGetContactsByName()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $SUT = static::$kernel->getContainer()->get('oktolab.contact_provider');

        $response = new Response(200);
        $response->setBody(base64_decode(file_get_contents(__DIR__.'/../DataFixtures/ContactProviderResults')));
        $plugin = new MockPlugin();
        $plugin->addResponse($response);
        $SUT->addSubscriber($plugin);


        $contacts = $SUT->getContactsByName('Schmi');
        $this->assertEquals(count($contacts), 24);
        $this->assertEquals($contacts[1]->getName(), 'Sara Goldschmidt');
        $this->assertEquals($contacts[1]->getGuid(), '4b51690b-702e-1d30-f03c-679f35e7b1f1');
        $this->assertEquals($contacts[1]->getFeePayed(), false);
    }

    public function testAddContactsToRent()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $SUT = static::$kernel->getContainer()->get('oktolab.contact_provider');

        $response = new Response(200);
        $response->setBody(base64_decode(file_get_contents(__DIR__.'/../DataFixtures/ContactProviderResults')));
        $plugin = new MockPlugin();
        $plugin->addResponse($response);
        $SUT->addSubscriber($plugin);


        $contacts = $SUT->getContactsByName('Schmi');

        $SUT->addContactsToRent($contacts);

        $entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $rentContacts = $entityManager->getRepository('OktolabRentBundle:Contact')->findAll();

        $this->assertEquals(count($rentContacts), 24);
    }
}
