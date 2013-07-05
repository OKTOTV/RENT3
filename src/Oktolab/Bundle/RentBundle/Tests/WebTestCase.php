<?php

namespace Oktolab\Bundle\RentBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class WebTestCase extends SymfonyWebTestCase
{
    protected $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->logIn();
    }

    protected function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewall   = 'secured_area';
        $token      = new UsernamePasswordToken('user', null, $firewall, array('ROLE_USER'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
