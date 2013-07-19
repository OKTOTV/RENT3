<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testDashboardPageRendersCorrectly()
    {
        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Returns Status-Code 200');
        $this->assertRegExp('/Dashboard/', $this->client->getResponse()->getContent());
    }

    public function testAboutPageRendersCorrectly()
    {
        $this->client->request('GET', '/about');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Returns Status-Code 200');
        $this->assertRegExp('/Lizenzen/', $this->client->getResponse()->getContent());
    }
}
