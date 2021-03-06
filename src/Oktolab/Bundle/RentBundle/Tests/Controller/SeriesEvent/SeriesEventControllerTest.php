<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\SeriesEvent;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * @author rs
 * SeriesEventControllerTest
 */
class SeriesEventControllerTest extends WebTestCase
{
    public function testCreateAnSeriesEvent()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ContactFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CostUnitFixture',
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\TimeblockFixture',
            ));
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $itemA = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'F00B5R'));
        $contact = $em->getRepository('OktolabRentBundle:Contact')->findOneBy(array('name' => 'John Appleseed'));
        $costunit = $em->getRepository('OktolabRentBundle:CostUnit')->findOneBy(array('guid' => '1234567DUMMY'));

        $crawler = $this->client->request('GET', '/rent/series_inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $form = $crawler->filter('.series-event')->form(
            array(
                'orb_series_event_form[end]'                => '2013-10-12 17:00:00',
                'orb_series_event_form[contact]'            => $contact->getId(),
                'orb_series_event_form[costunit]'           => $costunit->getId(),
                'orb_series_event_form[event_begin]'        => '2013-10-10 10:00:00',
                'orb_series_event_form[event_end]'          => '2013-10-11 10:00:00',
                'orb_series_event_form[repetition]'         => 7,
            )
        );
        $values = $form->getPhpValues();
        $values['orb_series_event_form']['objects'] = array(
            0 => array('type' => $itemA->getType(), 'object' => $itemA->getId())
        );

        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        // in finalize form. send this form to save the series event
        $form = $crawler->selectButton('Speichern')->form();
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect.');

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

    }

    public function testCreateSeriesEventDisplaysError()
    {
                $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ContactFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CostUnitFixture',
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\TimeblockFixture',
            ));
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $itemA = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'F00B5R'));
        $contact = $em->getRepository('OktolabRentBundle:Contact')->findOneBy(array('name' => 'John Appleseed'));
        $costunit = $em->getRepository('OktolabRentBundle:CostUnit')->findOneBy(array('guid' => '1234567DUMMY'));

        $crawler = $this->client->request('GET', '/rent/series_inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $form = $crawler->filter('.series-event')->form(
            array(
                'orb_series_event_form[end]'                => '2013-10-12 17:00:00',
                'orb_series_event_form[contact]'            => $contact->getId(),
                'orb_series_event_form[costunit]'           => $costunit->getId(),
                'orb_series_event_form[event_begin]'        => '2013-10-10 10:00:00',
                'orb_series_event_form[event_end]'          => '2013-10-19 10:00:00',
                'orb_series_event_form[repetition]'         => 7,
            )
        );
        $values = $form->getPhpValues();
        $values['orb_series_event_form']['objects'] = array(
            0 => array('type' => $itemA->getType(), 'object' => $itemA->getId())
        );

        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $this->assertEquals(
            1,
            $crawler->filter('body:contains("Die länge einer Buchung darf sich nicht mit dem Begin der nächsten Wiederholung überschneiden")')->count(),
            'Fehlernachricht nicht enthalten');
    }

    public function testFinalizeSeriesEventDisplaysError()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ContactFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CostUnitFixture',
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\TimeblockFixture',
            ));
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $itemA = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'F00B5R'));
        $contact = $em->getRepository('OktolabRentBundle:Contact')->findOneBy(array('name' => 'John Appleseed'));
        $costunit = $em->getRepository('OktolabRentBundle:CostUnit')->findOneBy(array('guid' => '1234567DUMMY'));

        $crawler = $this->client->request('GET', '/rent/series_inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $form = $crawler->filter('.series-event')->form(
            array(
                'orb_series_event_form[end]'                => '2013-10-12 17:00:00',
                'orb_series_event_form[contact]'            => $contact->getId(),
                'orb_series_event_form[costunit]'           => $costunit->getId(),
                'orb_series_event_form[event_begin]'        => '2013-10-10 10:00:00',
                'orb_series_event_form[event_end]'          => '2013-10-11 23:00:00',
                'orb_series_event_form[repetition]'         => 7,
            )
        );
        $values = $form->getPhpValues();
        $values['orb_series_event_form']['objects'] = array(
            0 => array('type' => $itemA->getType(), 'object' => $itemA->getId())
        );

        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        // in finalize form. send this form to save the series event
        $form = $crawler->selectButton('Speichern')->form();
        $crawler = $this->client->submit($form);

        $this->assertEquals(
            1,
            $crawler->filter('body:contains("Der Endzeitpunkt muss innerhalb der Verleihzeit liegen")')->count(),
            'Die Endzeit ist außerhalb des Zeitblocks'
        );
    }
}
