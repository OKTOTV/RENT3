<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testDashboard()
    {
        $this->client->request('GET', '/');
        $this->assertRegExp('/Dashboard/', $this->client->getResponse()->getContent());
    }
}
