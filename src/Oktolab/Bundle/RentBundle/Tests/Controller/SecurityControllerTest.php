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

        $response = $this->client->getResponse();
        $this->assertSame(302, $response->getStatusCode(), 'Response Status-Code is 302');
        $this->assertRegExp('/\/secure\/login$/', $response->headers->get('location'), 'Redirect URL to login page');
    }

    public function testCanAccessDashboardAfterLogin()
    {
        $this->logIn();

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful');
        $this->assertRegExp('/Dashboard/', $this->client->getResponse()->getContent(), 'Body contains /Dashboard/');
    }

    public function testCanNotAccessSearchBarWhileLoggedOut()
    {
        $crawler = $this->client->request('GET', '/secure/login');

        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful');
        $this->assertSame(0, $crawler->filter('#quicksearch')->count(), 'Can not access #quicksearch');
    }

    public function testClickLogoutWillDeleteSession()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful');

        $this->client->click($crawler->selectLink('Log out')->link());

        $this->assertRegExp(
            '/\/secure\/login$/',
            $this->client->getResponse()->headers->get('location'),
            'Redirects to Login Page'
        );

        $this->assertSame(
            null,
            $this->client->getContainer()->get('session')->get('_security_secured_area'),
            'Session is successfully destroyed'
        );
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
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response is successful');

        $this->client->followRedirect();
        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful');
        $this->assertRegExp('/Log In fehlgeschlagen/', $response->getContent(), 'Page contains error message');
    }

    /**
     * Logs user as "user" in
     *
     */
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
