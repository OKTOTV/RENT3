<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testDashboardPageRendersCorrectly()
    {
        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertRegExp('/Dashboard/', $this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isCacheable(), 'Response should be cacheable');
        $this->assertLessThan(24*60*60, $this->client->getResponse()->getTtl());
    }

    public function testAboutPageRendersCorrectly()
    {
        $this->client->request('GET', '/about');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertRegExp('/Lizenzen/', $this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isCacheable(), 'Response should be cacheable');
    }
}
