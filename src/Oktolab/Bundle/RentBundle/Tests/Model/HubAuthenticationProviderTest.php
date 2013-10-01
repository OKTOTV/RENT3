<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Model\UserToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response;
use Oktolab\Bundle\RentBundle\Model\GuzzleHubAuth;
use Oktolab\Bundle\RentBundle\Model\GuzzleHubSearch;

class HubAuthenticationProviderTest extends WebTestCase
{
    public function testAuthenticateWithCorrectCredentials()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $token = new UserToken();
        $token->setAttributes(array('username' => 'tu', 'password' => 'password'));

        $Authresponse = new Response(200);
        $Authresponse->setBody(base64_decode(file_get_contents(__DIR__.'/../DataFixtures/HubAuthContactcard')));

        $Authclient = static::$kernel->getContainer()->get('oktolab.hub_auth_service');
        $Authplugin = new MockPlugin();
        $Authplugin->addResponse($Authresponse);
        $Authclient->addSubscriber($Authplugin); //Mocks the response the auth_service gets

        $Searchresponse = new Response(200);
        $Searchresponse->setBody(base64_decode(file_get_contents(__DIR__.'/../DataFixtures/HubSearchContactcard')));

        $SearchClient = static::$kernel->getContainer()->get('oktolab.hub_search_service');
        $SearchPlugin = new MockPlugin();
        $SearchPlugin->addResponse($Searchresponse);
        $SearchClient->addSubscriber($SearchPlugin); //Mocks the respnse the search_service gets

        $SUT = static::$kernel->getContainer()->get('oktolab.hub_authentication_provider');
        $auth = $SUT->authenticate($token); //token should be now authenticated, have a role and contain the username

        $this->assertEquals($auth->isAuthenticated(), true);
        $this->assertEquals($auth->getUsername(), 'tu');

    }

    public function testAuthenticateWithWrongCredentials()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $token = new UserToken();
        $token->setAttributes(array('username' => 'none', 'password' => 'none'));

        $Authresponse = new Response(403);
        $Authresponse->setBody('0');

        $Authclient = static::$kernel->getContainer()->get('oktolab.hub_auth_service');
        $Authplugin = new MockPlugin();
        $Authplugin->addResponse($Authresponse);
        $Authclient->addSubscriber($Authplugin); //Mocks the response the auth_service gets

        $Searchresponse = new Response(200);
        $Searchresponse->setBody('a:1:{i:0;N;}');

        $SearchClient = static::$kernel->getContainer()->get('oktolab.hub_search_service');
        $SearchPlugin = new MockPlugin();
        $SearchPlugin->addResponse($Searchresponse);
        $SearchClient->addSubscriber($SearchPlugin); //Mocks the respnse the search_service gets

        $SUT = static::$kernel->getContainer()->get('oktolab.hub_authentication_provider');

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AuthenticationException');
        $SUT->authenticate($token);
    }
}
