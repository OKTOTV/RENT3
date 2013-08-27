<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Admin;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * Description of AdminControllerTest
 *
 * @author meh
 */
class AdminControllerTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures(array());
    }

    public function testCallIndexAsUserShouldFail()
    {
        $this->client->request('GET', '/admin');
        $this->assertTrue($this->client->getResponse()->isForbidden(), 'ROLE_USER must not have access.');
    }

    public function testCallIndexAsAdminUser()
    {
        $this->logIn('ROLE_ADMIN');
        $this->client->request('GET', '/admin');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'ROLE_ADMIN must have access to this page.');
    }
}
