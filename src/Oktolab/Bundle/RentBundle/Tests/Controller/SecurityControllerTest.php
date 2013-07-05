<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testAccessDeniedWithoutLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/about');

        $this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
    }
}
