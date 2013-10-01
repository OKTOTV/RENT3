<?php

namespace Oktolab\Bundle\RentBundle\Test\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response;

class HubAuthenticationProviderTest extends WebTestCase
{
    public function testGetContactCardByUsername()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $Searchresponse = new Response(200);
        $Searchresponse->setBody(base64_decode(file_get_contents(__DIR__.'/../DataFixtures/HubSearchContactcard')));

        $SearchClient = static::$kernel->getContainer()->get('oktolab.hub_search_service');
        $SearchPlugin = new MockPlugin();
        $SearchPlugin->addResponse($Searchresponse);
        $SearchClient->addSubscriber($SearchPlugin); //Mocks the respnse the search_service gets

        $SUT = static::$kernel->getContainer()->get('oktolab.hub_search_service');

        $contactcard = $SUT->getContactCardForUser('tu');
        $this->assertEquals($contactcard->getGuid(), 'tu');
    }
}
