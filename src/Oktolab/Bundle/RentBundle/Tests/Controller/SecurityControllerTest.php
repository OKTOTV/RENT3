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
        $this->client->request('GET', '/');
        $this->assertRegExp('/\/secure\/login$/', $this->client->getResponse()->headers->get('location'));
    }

    public function testCanAccessDashboardAfterLogin()
    {
        $this->logIn();

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertRegExp('/Dashboard/', $this->client->getResponse()->getContent());
    }

    public function testCanNotAccessSearchBarWhileLoggedOut()
    {
        $crawler = $this->client->request('GET', '/secure/login');
        $this->assertSame(0, $crawler->filter('#quicksearch')->count());
    }

    public function testClickLogoutWillDeleteSession()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->client->click($crawler->selectLink('Log out')->link());

        // tests redirection to login page
        $this->assertRegExp('/\/secure\/login$/', $this->client->getResponse()->headers->get('location'));

        // verifies deleted session
        $this->assertSame(null, $this->client->getContainer()->get('session')->get('_security_secured_area'));
    }

    public function testFailedLoginWillThrowError()
    {
        $crawler = $this->client->request('GET', '/secure/login');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $buttonCrawlerNode = $crawler->selectButton('Log In');
        $form = $buttonCrawlerNode->form(
            array(
                '_username'  => 'Fabien',
                '_password'  => 'Symfony rocks!',
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertRegExp('/Log In fehlgeschlagen/', $this->client->getResponse()->getContent());
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
