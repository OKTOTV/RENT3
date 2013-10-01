<?php

namespace Oktolab\Bundle\RentBundle\Test\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response;

class HubSearchServiceTest extends WebTestCase
{
    public function testGetContactCardByCredentials()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $authResponse = new Response(200);
        $authResponse->setBody(base64_decode(file_get_contents(__DIR__.'/../DataFixtures/HubAuthContactcard')));

        $SearchClient = static::$kernel->getContainer()->get('oktolab.hub_auth_service');
        $SearchPlugin = new MockPlugin();
        $SearchPlugin->addResponse($authResponse);
        $SearchClient->addSubscriber($SearchPlugin); //Mocks the respnse the search_service gets

        $SUT = static::$kernel->getContainer()->get('oktolab.hub_auth_service');

        $contactcard = $SUT->getContactCardForUserByAuthentication('tu', 'password');
        $this->assertEquals($contactcard['uid'][0], 'tu');
    }

    public function testExceptionByWrongCredentials()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $authResponse = new Response(403);
        $authResponse->setBody('0');

        $SearchClient = static::$kernel->getContainer()->get('oktolab.hub_auth_service');
        $SearchPlugin = new MockPlugin();
        $SearchPlugin->addResponse($authResponse);
        $SearchClient->addSubscriber($SearchPlugin); //Mocks the respnse the search_service gets

        $this->setExpectedException('\Symfony\Component\Security\Core\Exception\BadCredentialsException');
        $SUT = static::$kernel->getContainer()->get('oktolab.hub_auth_service');
        $SUT->getContactCardForUserByAuthentication('none', 'none');
    }
}
