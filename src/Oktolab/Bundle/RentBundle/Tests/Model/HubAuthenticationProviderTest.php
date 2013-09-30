<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Model\UserToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Guzzle\Http\Client;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response;
use Oktolab\Bundle\RentBundle\Model\GuzzleHubAuth;

class HubAuthenticationProviderTest extends WebTestCase
{
    public function testAuthenticate()
    {
        //TODO: Mock Guzzle Response for HubUserProvider
        $token = new UserToken();
        $token->setAttributes(array('username' => 'admin', 'password' => 'adminpass'));

        $response = new Response(200);
        $response->setBody(file_get_contents(__DIR__.'/../DataFixtures/HubAuthContactcard'));

        $client = new GuzzleHubAuth('http://example.com');
        $plugin = new MockPlugin();
        $plugin->addResponse($response);
        $client->addSubscriber($plugin);

        static::$kernel = static::createKernel();
        static::$kernel->boot();
        static::$kernel->getContainer()->set('oktolab.guzzle_hub_auth', $client);
        $SUT = static::$kernel->getContainer()->get('oktolab.hub_authentication_provider');


        $auth = $SUT->authenticate($token);

        $this->assertEquals($auth, true);

    }
}
