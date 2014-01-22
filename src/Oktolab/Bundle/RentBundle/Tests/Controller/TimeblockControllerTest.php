<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

class TimeblockControllerTest extends WebTestCase
{
    private $em = null;

    private function getEm()
    {
        if ($this->em == null) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        }
        return $this->em;
    }

    /**
     * @test
     */
    public function listAllTimeBlocks()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\EventApiTimeblockFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));

        $this->client->request('GET', '/admin/timeblock/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $this->assertEquals(1, $this->client->getCrawler()->filter('.aui-page-panel-content table tbody tr')->count());
    }

    /**
     * @test
     */
    public function showTimeBlock()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\EventApiTimeblockFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));

        $timeblock = $this->getEm()->getRepository('OktolabRentBundle:Timeblock')->findOneBy(array('title' => 'testtimeblock'));

        $this->client->request('GET', '/admin/timeblock/'.$timeblock->getId());
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $crawler = $this->client->getCrawler();
        $this->assertEquals(1, $crawler->filter('div.field-group.viewMode span:contains("Montag, Dienstag, Mittwoch, Donnerstag, Freitag, Samstag, Sonntag")')->count(), 'Weekdays not found.');
        $this->assertEquals(1, $crawler->filter('div.field-group.viewMode span:contains("testtimeblock")')->count(), 'No name found');
    }

    /**
     * @test
     */
    public function updateTimeBlock()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\EventApiTimeblockFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));

        $timeblock = $this->getEm()->getRepository('OktolabRentBundle:Timeblock')->findOneBy(array('title' => 'testtimeblock'));

        $this->client->request('GET', '/admin/timeblock/'.$timeblock->getId().'/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_timeblock[title]'  => 'Foo',
            )
        );
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirection');
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(1, $crawler->filter('div.field-group.viewMode span:contains("Foo")')->count(), 'Title should be Foo');
    }

    /**
     * test
     */
    public function createTimeBlock()
    {
        $this->loadFixtures(array('\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'));
        $this->logIn('ROLE_ADMIN');
        $this->client->request('GET', '/admin/timeblock/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $form = $this->client->getCrawler()->selectButton('Speichern')->form(
            array(
                'oktolab_bundle_rentbundle_timeblock[title]'         => 'Foo',
                'oktolab_bundle_rentbundle_timeblock[intervalBegin]' => '2000-01-01',
                'oktolab_bundle_rentbundle_timeblock[intervalEnd]'   => '2020-01-01',
                'oktolab_bundle_rentbundle_timeblock[weekdays][0]'    => true,
                'oktolab_bundle_rentbundle_timeblock[weekdays][1]'    => true,
                'oktolab_bundle_rentbundle_timeblock[begin][hour]'   => 8,
                'oktolab_bundle_rentbundle_timeblock[begin][minute]' => 0,
                'oktolab_bundle_rentbundle_timeblock[end][hour]'     => 18,
                'oktolab_bundle_rentbundle_timeblock[end][minute]'   => 0,
                'oktolab_bundle_rentbundle_timeblock[isActive]'      => true,
            )
        );
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirection(), 'Response should be a redirection');
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');
        $this->assertEquals(1, $crawler->filter('div.field-group.viewMode span:contains("Montag, Dienstag")')->count(), 'Weekdays not found.');
        $this->assertEquals(1, $crawler->filter('div.field-group.viewMode span:contains("Foo")')->count(), 'No name found');
    }


    /**
     * test
     */
    public function deleteTimeBlock()
    {
        $this->logIn('ROLE_ADMIN');
        $this->loadFixtures(array(
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\EventApiTimeblockFixture',
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));

        $timeblock = $this->getEm()->getRepository('OktolabRentBundle:Timeblock')->findOneBy(array('title' => 'testtimeblock'));

        $this->client->request('GET', '/admin/timeblock/');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $this->assertEquals(1, $this->client->getCrawler()->filter('.aui-page-panel-content table tbody tr')->count());

        $this->client->request('GET', '/admin/timeblock/'.$timeblock->getId().'/delete');
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirection');
        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $this->assertEquals(0, $this->client->getCrawler()->filter('.aui-page-panel-content table tbody tr')->count());
    }
}
