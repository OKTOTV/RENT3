<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Admin;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * Description of CostUnitControllerTest
 *
 * @author rs
 */
class CostUnitControllerTest extends WebTestCase
{
    public function testSubmitFormToCreateNewCostUnit()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'));

        $this->client->request('GET', '/admin/costunit/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_costunit[name]' => 'Testcostunit',
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect');

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(
            1,
            $crawler->filter('.aui-page-panel-content:contains("Testcostunit")')->count(),
            'The Costunit title should appear on this page.'
        );
    }

    public function testSubmitFormToUpdateCostUnit()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CostUnitFixture',
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));

        $em       = $this->getContainer()->get('doctrine.orm.entity_manager');
        $costunit = $em->getRepository('OktolabRentBundle:CostUnit')->findOneBy(array('guid' => '1234567DUMMY'));

        // load page
        $this->client->request('GET', '/admin/costunit/'.$costunit->getId().'/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

        // fill form and submit it
        $this->client->submit(
            $this->client->getCrawler()->selectButton('Speichern')->form(),
            array('oktolab_bundle_rentbundle_costunit[name]' => 'Changed Costunit Name')
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect');

        // check redirect and page content
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(
            1,
            $crawler->filter('.aui-page-panel-content:contains("Changed Costunit Name")')->count(),
            'The new Costunit title should appear on this page.'
        );
    }

    public function testAddContactToCostUnit()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CostUnitFixture',
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));

        $em       = $this->getContainer()->get('doctrine.orm.entity_manager');
        $costunit = $em->getRepository('OktolabRentBundle:CostUnit')->findOneBy(array('guid' => '1234567DUMMY'));

        $this->client->request('GET', '/admin/costunit/'.$costunit->getId().'/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $form = $this->client->getCrawler()->selectButton('Speichern')->form();
        $this->client->request(
            'PUT',
            $form->getUri(),
            array(
                'oktolab_bundle_rentbundle_costunit' => array(
                    '_token'    => $form['oktolab_bundle_rentbundle_costunit[_token]']->getValue(),
                    'name'      => 'KostenstelleNEW',
                    'contacts'  => array(1 => '12345678')
                )
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect');
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(
            1,
            $crawler->filter('.aui-page-panel-content:contains("KostenstelleNEW")')->count(),
            'The new Costunit title should appear on this page.'
        );

    }

    /**
     * @test
     */
    public function removeContactFromCostUnit()
    {
        $this->markTestIncomplete();
    }

    public function testDeleteCostUnitWithMembersWillFail()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CostUnitWithContactFixture',
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));

        $em       = $this->getContainer()->get('doctrine.orm.entity_manager');
        $costunit = $em->getRepository('OktolabRentBundle:CostUnit')->findOneBy(array('guid' => '1234567DUMMY'));

        $this->client->request('GET', '/admin/costunit/page');

        $this->client->request('GET', '/admin/costunit/'.$costunit->getId().'/delete');
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect');
        $crawler = $this->client->followRedirect();

        $this->assertEquals(
            1,
            $crawler->filter('.aui-page-panel-content table tbody tr')->count(),
            'The Costunit should not be deleted.'
        );
    }

    public function testSuccessfullyDeleteACostUnit()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CostUnitFixture',
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));

        $em       = $this->getContainer()->get('doctrine.orm.entity_manager');
        $costunit = $em->getRepository('OktolabRentBundle:CostUnit')->findOneBy(array('guid' => '1234567DUMMY'));

        $this->client->request('GET', '/admin/costunit/'.$costunit->getId().'/delete');
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(
            0,
            $crawler->filter('.aui-page-panel-content table tbody tr')->count(),
            'The Costunit should be deleted.'
        );
    }
}
