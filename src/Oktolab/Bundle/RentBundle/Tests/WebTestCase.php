<?php

namespace Oktolab\Bundle\RentBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase as LiipWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class WebTestCase extends LiipWebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->logIn();
    }

    /**
     * Logs the user  in.
     * @param type $role (ROLE_USER, ROLE_ADMIN)
     */
    protected function logIn($role = 'ROLE_USER')
    {
        if (in_array($role, array('ROLE_USER', 'ROLE_ADMIN'))) {
            $session = $this->client->getContainer()->get('session');

            $firewall = 'secured_area';
            $token    = new UsernamePasswordToken('user', null, $firewall, array($role));
            $session->set('_security_'.$firewall, serialize($token));
            $session->save();

            $cookie = new Cookie($session->getName(), $session->getId());
            $this->client->getCookieJar()->set($cookie);
        }
    }

    public function tearDown(){
        $this->getContainer()->get('doctrine')->getConnection()->close();
        parent::tearDown();
    }
}
