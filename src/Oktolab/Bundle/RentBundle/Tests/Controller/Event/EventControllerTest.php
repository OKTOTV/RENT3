<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Event;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Qms;
/**
 * Event Controller Test
 *
 * @group Event
 */
class EventControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function createAnEventWithItems()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ContactFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CostUnitFixture',
                'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
            )
        );

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $itemA = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'F00B5R'));
        $itemB = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'B5ZF00'));
        $contact = $em->getRepository('OktolabRentBundle:Contact')->findOneBy(array('name' => 'John Appleseed'));
        $costunit = $em->getRepository('OktolabRentBundle:CostUnit')->findOneBy(array('guid' => '1234567DUMMY'));

        $crawler = $this->client->request('GET', '/rent/inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $form = $crawler->filter('#OktolabRentBundle_Event_Form_update')->form(
            array(
                'OktolabRentBundle_Event_Form[name]'        => 'My Event',
                'OktolabRentBundle_Event_Form[begin]'       => '2013-10-11 12:00:00',
                'OktolabRentBundle_Event_Form[end]'         => '2013-10-12 17:00:00',
                'OktolabRentBundle_Event_Form[contact]'     => $contact->getId(),
                'OktolabRentBundle_Event_Form[costunit]'    => $costunit->getId()
            )
        );

        $values = $form->getPhpValues();
        $values['OktolabRentBundle_Event_Form']['objects'] = array(
            0 => array('object' => $itemA->getId(), 'type' => $itemA->getType()),
            1 => array('object' => $itemB->getId(), 'type' => $itemB->getType()),
        );

        // thx to: https://github.com/symfony/symfony/issues/4124#issuecomment-13229362
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be a redirect.');

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $event = $em->getRepository('OktolabRentBundle:Event')->findOneBy(array('name' => 'My Event'));
        $this->assertSame('My Event', $event->getName());
        $this->assertSame(Event::STATE_PREPARED, $event->getState(), 'State should be "PREPARED".');
        $this->assertEquals(new \DateTime('2013-10-11 12:00:00'), $event->getBegin());
        $this->assertEquals(new \DateTime('2013-10-12 17:00:00'), $event->getEnd());

        $eventObjects = $event->getObjects();
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\EventObject', $eventObjects[0]);
        $this->assertSame($itemA->getId(), $eventObjects[0]->getObject());
        $this->assertSame($itemA->getType(), $eventObjects[0]->getType());

        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\EventObject', $eventObjects[1]);
        $this->assertSame($itemB->getId(), $eventObjects[1]->getObject());
        $this->assertSame($itemB->getType(), $eventObjects[1]->getType());
    }

    /**
     * @depends createAnEventWithItems
     * @test
     */
    public function createAnEventDisplaysErrorsIfFormInputIsInvalid()
    {
        $this->loadFixtures(array(
            '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
            'Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
        ));
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $item = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'F00B5R'));

        $crawler = $this->client->request('GET', '/rent/inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $form = $crawler->filter('#OktolabRentBundle_Event_Form_update')->form(
            array(
                'OktolabRentBundle_Event_Form[name]'  => 'asdf',
                'OktolabRentBundle_Event_Form[begin]' => '2013-10-11 12:00:00',
                'OktolabRentBundle_Event_Form[end]'   => '2013-10-10 12:00:00', //invalid data
            )
        );

        $values = $form->getPhpValues();
        $values['OktolabRentBundle_Event_Form']['objects'] = array(0 => array('object' => $item->getId(), 'type' => $item->getType()));

        // @see https://github.com/symfony/symfony/issues/4124#issuecomment-13229362
        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');
        $this->assertRegExp('/Das Event konnte nicht gespeichert werden/', $this->client->getResponse()->getContent());

        $formValues = $crawler->filter('#content')->selectButton('Speichern')->form()->getValues();
        $this->assertSame($item->getType(), $formValues['OktolabRentBundle_Event_Form[objects][0][type]']);
        $this->assertSame((string)$item->getId(), $formValues['OktolabRentBundle_Event_Form[objects][0][object]']);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $count = $em->createQuery('SELECT COUNT(e.id) FROM OktolabRentBundle:Event e')->getSingleScalarResult();
        $this->assertEquals(0, $count, 'No Event was created.');
    }

    /**
     * @test
     */
    public function createAnEventDisplaysErrorIfEventObjectNotFound()
    {
        $this->markTestIncomplete('Validation not implemented.');
    }

    /**
     * @test
     */
    public function createAnEventAddsToLog()
    {
        $this->markTestIncomplete('How to monitor Log? Symfony-Profiler?');
    }

    /**
     * @test
     */
    public function editAnEventReturnsValidResponse()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
            )
        );
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $event = $em->getRepository('OktolabRentBundle:Event')->findOneBy(array('name' => 'My Event'));

        $crawler = $this->client->request('GET', '/event/'.$event->getId().'/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $filter = '#content form > input[name="_method"][value="PUT"]';
        $this->assertEquals(1, $crawler->filter($filter)->count(), 'Form method was expected to be "PUT"');

        $form = $crawler->filter('#content')->selectButton('Speichern')->form();
        $url = $this->client->getContainer()
                ->get('router')
                ->generate('OktolabRentBundle_Event_Update', array('id' => $event->getId()));

        $this->assertStringEndsWith($url, $form->getUri(), sprintf('Form action was expected to be "%s".', $url));
    }

    /**
     * @depends editAnEventReturnsValidResponse
     * @test
     */
    public function editAnEventWithValidData()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
            )
        );
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $event1 = $em->getRepository('OktolabRentBundle:Event')->findOneBy(array('name' => 'My Event'));
        $em->detach($event1);

        $this->client->request('GET', '/event/'.$event1->getId().'/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $form = $this->client->getCrawler()->filter('#content')->selectButton('Speichern')->form(
            array(
                'OktolabRentBundle_Event_Form[name]' => 'new name',
                'OktolabRentBundle_Event_Form[end]'  => '2013-10-16 17:00:00',
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response should be redirect.');

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $event = $this->getContainer()
                ->get('doctrine.orm.entity_manager')
                ->getRepository('OktolabRentBundle:Event')
                ->findOneBy(array('id' => $event1->getId()));
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\Event', $event);
        $this->assertEquals(new \DateTime('2013-10-16 17:00:00'), $event->getEnd());
        $this->assertSame('new name', $event->getName());
        $this->assertSame(Event::STATE_PREPARED, $event->getState());
    }

    /**
     * @depends editAnEventReturnsValidResponse
     * @test
     */
    public function editAnEventWillAddEventObject()
    {
        $this->markTestIncomplete();
    }

    /**
     * @depends editAnEventReturnsValidResponse
     * @test
     */
    public function editAnEventWillRemoveEventObject()
    {
        $this->markTestIncomplete();
    }

    /**
     * @depends editAnEventReturnsValidResponse
     * @test
     */
    public function editAnEventWithInvalidData()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
            )
        );
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $event = $em->getRepository('OktolabRentBundle:Event')->findOneBy(array('name' => 'My Event'));

        $crawler = $this->client->request('GET', '/event/'.$event->getId().'/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $form = $crawler->filter('#content')->selectButton('Speichern');
        $this->assertSame(1, $form->count(), 'The EventForm is rendered');

        // set to invalid data
        $form = $form->form(array('OktolabRentBundle_Event_Form[name]' => ''));
        $this->client->submit($form);

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertRegExp('/Das Event konnte nicht gespeichert werden/', $response->getContent());

        $crawler = $this->client->getCrawler();
        $fieldError = $crawler->filter('#OktolabRentBundle_Event_Form_name ~ div[class="error"]');

        $this->assertSame(1, $fieldError->count(), 'An error message is rendered.');
        $this->assertRegExp('/Dieser Wert sollte nicht leer sein./', $fieldError->html());

        $this->assertEquals(
            1,
            $crawler->filter('#content form > input[name="_method"][value="PUT"]')->count(),
            'Form method is "PUT"'
        );

        $form = $crawler->filter('#content')->selectButton('Speichern')->form();
        $url = $this->client->getContainer()
                ->get('router')
                ->generate('OktolabRentBundle_Event_Update', array('id' => $event->getId()));

        $this->assertStringEndsWith($url, $form->getUri(), sprintf('Form action is "%s".', $url));
    }

    /**
     * @depends editAnEventWithInvalidData
     * @test
     */
    public function editAnEventRendersEventObjectsAfterSubmitWithInvalidData()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
            )
        );
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $event = $em->getRepository('OktolabRentBundle:Event')->findOneBy(array('name' => 'My Event'));
        $item = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'YXCV'));

        $crawler = $this->client->request('GET', '/event/'.$event->getId().'/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $form = $crawler->filter('#content')->selectButton('Speichern');
        $this->assertSame(1, $form->count(), 'The EventForm is rendered');

        $form = $form->form(array('OktolabRentBundle_Event_Form[name]' => '')); // set to invalid data
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');
        $this->assertRegExp('/Das Event konnte nicht gespeichert werden/', $this->client->getResponse()->getContent());

        $formValues = $this->client->getCrawler()->filter('#content')->selectButton('Speichern')->form()->getValues();
        $this->assertSame('item', $formValues['OktolabRentBundle_Event_Form[objects][0][type]']);
        $this->assertSame((string)$item->getId(), $formValues['OktolabRentBundle_Event_Form[objects][0][object]']);
    }

    /**
     * @test
     */
    public function rentAnEventReturnsValidResponse()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture'
            )
        );
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $event = $em->getRepository('OktolabRentBundle:Event')->findOneBy(array('name' => 'My Event'));

        $crawler = $this->client->request('GET', '/event/'.$event->getId().'/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $form = $crawler->filter('#content')->selectButton('Ausgeben')->form(
            array(
                'OktolabRentBundle_Event_Form[objects][0][scanned]' => '1',
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response is a redirect.');

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');
        $this->assertRegExp('/Event erfolgreich ausgegeben/', $this->client->getResponse()->getContent());
    }

    /**
     * @test
     */
    public function rentItemInSameTimerangeGetsValidationError()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ContactFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CostUnitFixture'
            )
        );

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $itemNotFree = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'YXCV'));
        $itemB = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'B5ZF00'));
        $contact = $em->getRepository('OktolabRentBundle:Contact')->findOneBy(array('name' => 'John Appleseed'));
        $costunit = $em->getRepository('OktolabRentBundle:CostUnit')->findOneBy(array('guid' => '1234567DUMMY'));

        $crawler = $this->client->request('GET', '/rent/inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $form = $crawler->filter('#OktolabRentBundle_Event_Form_update')->form(
            array(
                'OktolabRentBundle_Event_Form[name]'        => 'My Event',
                'OktolabRentBundle_Event_Form[begin]'       => '2013-10-14 12:00:00',
                'OktolabRentBundle_Event_Form[end]'         => '2013-10-16 17:00:00',
                'OktolabRentBundle_Event_Form[contact]'     => $contact->getId(),
                'OktolabRentBundle_Event_Form[costunit]'    => $costunit->getId()
            )
        );

        $values = $form->getPhpValues();
        $values['OktolabRentBundle_Event_Form']['objects'] = array(
            0 => array('object' => $itemNotFree->getId(), 'type' => $itemNotFree->getType()),
            1 => array('object' => $itemB->getId(), 'type' => $itemB->getType()),
        );

        // thx to: https://github.com/symfony/symfony/issues/4124#issuecomment-13229362
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $crawler = $this->client->getCrawler();
        $fieldError = $crawler->filter('div[class="error"]');

        $this->assertSame(1, $fieldError->count(), 'An error message is rendered.');
        $this->assertRegExp('/'.$itemNotFree->getTitle().'/', $fieldError->html());
    }

     /**
     * @test
     */
    public function testQmsFormWithoutDescription()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\QmsFixture'
            ));

       $em = $this->getContainer()->get('doctrine.orm.entity_manager');
       $event = $em->getRepository('OktolabRentBundle:Event')->findOneBy(array('name' => 'My Event'));

       $this->client->request('GET', '/event/'.$event->getId().'/check');
       $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

       $form = $this->client->getCrawler()->selectButton('Abschließen')->form(
            array(
                'ORB_Event_QMS_Check_Form[qmss][0][status]'                 => Qms::STATE_OKAY,
                'ORB_Event_QMS_Check_Form[qmss][0][description]'            => '',
                'ORB_Event_QMS_Check_Form[qmss][1][status]'                 => Qms::STATE_FLAW,
                'ORB_Event_QMS_Check_Form[qmss][1][description]'            => '',
                'ORB_Event_QMS_Check_Form[qmss][2][status]'                 => Qms::STATE_DAMAGED,
                'ORB_Event_QMS_Check_Form[qmss][2][description]'            => '',
                'ORB_Event_QMS_Check_Form[qmss][3][status]'                 => Qms::STATE_DESTROYED,
                'ORB_Event_QMS_Check_Form[qmss][3][description]'            => '',
                'ORB_Event_QMS_Check_Form[qmss][4][status]'                 => Qms::STATE_LOST,
                'ORB_Event_QMS_Check_Form[qmss][4][description]'            => '',
            )
        );
       $this->client->submit($form);
       $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
       $fieldError = $this->client->getCrawler()->filter('div[class="error"]');
       $this->assertEquals(4, $fieldError->count(), 'There should  be exact 4 errors.');
    }

    /**
     * @test
     */
    public function testQmsFormWithDescription()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventTypeFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\QmsFixture'
            ));

       $em = $this->getContainer()->get('doctrine.orm.entity_manager');
       $event = $em->getRepository('OktolabRentBundle:Event')->findOneBy(array('name' => 'My Event'));

       $this->client->request('GET', '/event/'.$event->getId().'/check');
       $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

       $form = $this->client->getCrawler()->selectButton('Abschließen')->form(
            array(
                'ORB_Event_QMS_Check_Form[qmss][0][status]'                 => Qms::STATE_OKAY,
                'ORB_Event_QMS_Check_Form[qmss][0][description]'            => 'random description',
                'ORB_Event_QMS_Check_Form[qmss][1][status]'                 => Qms::STATE_FLAW,
                'ORB_Event_QMS_Check_Form[qmss][1][description]'            => 'random description',
                'ORB_Event_QMS_Check_Form[qmss][2][status]'                 => Qms::STATE_DAMAGED,
                'ORB_Event_QMS_Check_Form[qmss][2][description]'            => 'random description',
                'ORB_Event_QMS_Check_Form[qmss][3][status]'                 => Qms::STATE_DESTROYED,
                'ORB_Event_QMS_Check_Form[qmss][3][description]'            => 'random description',
                'ORB_Event_QMS_Check_Form[qmss][4][status]'                 => Qms::STATE_LOST,
                'ORB_Event_QMS_Check_Form[qmss][4][description]'            => 'random description',
            )
        );
       $this->client->submit($form);
       $this->assertTrue($this->client->getResponse()->isRedirection(), 'Response should be a redirection');
       $this->client->followRedirect();
       $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful');

       $successMessage = $this->client->getCrawler()->filter('div[class="aui-message success"]');
       $this->assertEquals(1, $successMessage->count(), 'There should be a success message.');
       $this->assertRegExp('/Event erfolgreich abgeschlossen/', $successMessage->html());
    }
}
