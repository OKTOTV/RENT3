<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Admin;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * Settings Controller Tests
 *
 * @author meh
 */
class SettingsControllerTest extends WebTestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->logIn('ROLE_ADMIN');
    }

    /**
     * @test
     */
    public function indexAction()
    {
        $crawler = $this->client->request('GET', '/admin/settings');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $this->assertRegExp('/inventory.setting.companySetting/', $crawler->filter('#content')->text());
    }

    /**
     * @test
     */
    public function viewCompanySettings()
    {
        $crawler = $this->client->request('GET', '/admin/settings/company');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $this->assertRegExp('/inventory.setting.companySetting/', $crawler->filter('#content')->text());
    }

    /**
     * @test
     */
    public function editCompanySettings()
    {
        $crawler = $this->client->request('GET', '/admin/settings/company/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $this->assertRegExp('/admin.setting.edit/', $crawler->filter('#content')->text());
        $this->assertCount(1, $crawler->filter('#content form'));
    }

    /**
     * @depends editCompanySettings
     * @test
     */
    public function updateCompanySettings()
    {
        $crawler = $this->client->request('GET', '/admin/settings/company/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $form = $crawler->filter('#content form')->selectButton('Speichern')->form(
            array(
                'oktolab_setting_companytype[name]'     => 'Oktolab GmbH',
                'oktolab_setting_companytype[adress]'   => 'Example Street 123',
                'oktolab_setting_companytype[place]'    => 'Vienna',
                'oktolab_setting_companytype[plz]'      => '4242',
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response is a redirect.');

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $this->assertRegExp('/Oktolab GmbH/', $crawler->filter('#content')->text());
        $this->assertRegExp('/Example Street 123/', $crawler->filter('#content')->text());
    }
}
