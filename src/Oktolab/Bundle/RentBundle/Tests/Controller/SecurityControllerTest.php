<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testWillRedirectToLoginPage()
    {
        $this->client->request('GET', '/about');
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertRegExp('/Log In/', $this->client->getResponse()->getContent());
    }

    public function testCanAccessDashboardAfterLogin()
    {
        $this->logIn();

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertRegExp('/Dashboard/', $this->client->getResponse()->getContent());
    }

    public function testClickLogoutWillDeleteSession()
    {
        $this->markTestIncomplete('dunno know how');
    }

    private function logIn()
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
