<?php

namespace Oktolab\Bundle\RentBundle\Tests\Controller\Event;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Oktolab\Bundle\RentBundle\Entity\Event;

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
    public function createAnEventReturnsValidResponse()
    {
        $this->client->request('POST', '/event');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');
        $this->assertFalse($this->client->getResponse()->isCacheable(), 'Response must not be cacheable.');
    }

    /**
     * @depends createAnEventReturnsValidResponse
     * @test
     */
    public function createAnEventWithItems()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\ContactFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\CostUnitFixture'
            )
        );
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $itemA = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'F00B5R'));
        $itemB = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('barcode' => 'B5ZF00'));

        $crawler = $this->client->request('GET', '/rent/inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $form = $crawler->filter('#OktolabRentBundle_Event_Form_update')->form(
            array(
                'OktolabRentBundle_Event_Form[name]'        => 'My Event',
                'OktolabRentBundle_Event_Form[description]' => 'Some notices about this event. It is a test.',
                'OktolabRentBundle_Event_Form[begin]'       => '2013-10-11 12:00:00',
                'OktolabRentBundle_Event_Form[end]'         => '2013-10-12 17:00:00',
                'OktolabRentBundle_Event_Form[contact]'     => 1,
                'OktolabRentBundle_Event_Form[costunit]'    => 1
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

        $event = $em->getRepository('OktolabRentBundle:Event')->findOneBy(array('id' => 1));
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
     * @depends createAnEventReturnsValidResponse
     * @test
     */
    public function createAnEventDisplaysErrorsIfFormInputIsInvalid()
    {
        $this->loadFixtures(array('\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture'));

        $crawler = $this->client->request('GET', '/rent/inventory');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $form = $crawler->filter('#OktolabRentBundle_Event_Form_update')->form(
            array(
                'OktolabRentBundle_Event_Form[name]'  => 'asdf',
                'OktolabRentBundle_Event_Form[begin]' => '2013-10-11 12:00:00',
                'OktolabRentBundle_Event_Form[end]'   => '', //invalid data
            )
        );

        $values = $form->getPhpValues();
        $values['OktolabRentBundle_Event_Form']['objects'] = array(0 => array('object' => '1', 'type' => 'item'));

        // @see https://github.com/symfony/symfony/issues/4124#issuecomment-13229362
        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');
        $this->assertRegExp('/There was an error while saving the form./', $this->client->getResponse()->getContent());

        $formValues = $crawler->filter('#content')->selectButton('Update')->form()->getValues();
        $this->assertSame('item', $formValues['OktolabRentBundle_Event_Form[objects][0][type]']);
        $this->assertSame('1', $formValues['OktolabRentBundle_Event_Form[objects][0][object]']);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $count = $em->createQuery('SELECT COUNT(e.id) FROM OktolabRentBundle:Event e')->getSingleScalarResult();
        $this->assertEquals(0, $count, 'No Event was created.');
    }

    /**
     * @depends createAnEventReturnsValidResponse
     * @test
     */
    public function createAnEventDisplaysErrorIfEventObjectNotFound()
    {
        $this->markTestIncomplete('Validation not implemented.');
    }

    /**
     * @depends createAnEventReturnsValidResponse
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
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
            )
        );

        $crawler = $this->client->request('GET', '/event/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $filter = '#content form > input[name="_method"][value="PUT"]';
        $this->assertEquals(1, $crawler->filter($filter)->count(), 'Form method was expected to be "PUT"');

        $form = $crawler->filter('#content')->selectButton('Update')->form();
        $url = $this->client->getContainer()
                ->get('router')
                ->generate('OktolabRentBundle_Event_Update', array('id' => 1));

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
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
            )
        );

        $this->client->request('GET', '/event/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response should be successful.');

        $form = $this->client->getCrawler()->filter('#content')->selectButton('Update')->form(
            array(
                'OktolabRentBundle_Event_Form[name]' => 'I edited the name',
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
                ->findOneBy(array('id' => 1));

        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\Event', $event);
        $this->assertEquals(new \DateTime('2013-10-16 17:00:00'), $event->getEnd());
        $this->assertSame('I edited the name', $event->getName());
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
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
            )
        );

        $crawler = $this->client->request('GET', '/event/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $form = $crawler->filter('#content')->selectButton('Update');
        $this->assertSame(1, $form->count(), 'The EventForm is rendered');

        // set to invalid data
        $form = $form->form(array('OktolabRentBundle_Event_Form[name]' => ''));
        $this->client->submit($form);

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Response is successful.');
        $this->assertRegExp('/There was an error while saving the form./', $response->getContent());

        $crawler = $this->client->getCrawler();
        $fieldError = $crawler->filter('#OktolabRentBundle_Event_Form_name ~ div[class="error"]');

        $this->assertSame(1, $fieldError->count(), 'An error message is rendered.');
        $this->assertRegExp('/Dieser Wert sollte nicht leer sein./', $fieldError->html());

        $this->assertEquals(
            1,
            $crawler->filter('#content form > input[name="_method"][value="PUT"]')->count(),
            'Form method is "PUT"'
        );

        $form = $crawler->filter('#content')->selectButton('Update')->form();
        $url = $this->client->getContainer()
                ->get('router')
                ->generate('OktolabRentBundle_Event_Update', array('id' => 1));

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
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
            )
        );

        $crawler = $this->client->request('GET', '/event/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $form = $crawler->filter('#content')->selectButton('Update');
        $this->assertSame(1, $form->count(), 'The EventForm is rendered');

        $form = $form->form(array('OktolabRentBundle_Event_Form[name]' => '')); // set to invalid data
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');
        $this->assertRegExp('/There was an error while saving the form./', $this->client->getResponse()->getContent());

        $formValues = $this->client->getCrawler()->filter('#content')->selectButton('Update')->form()->getValues();
        $this->assertSame('item', $formValues['OktolabRentBundle_Event_Form[objects][0][type]']);
        $this->assertSame('1', $formValues['OktolabRentBundle_Event_Form[objects][0][object]']);
    }

    /**
     * @test
     */
    public function rentAnEventReturnsValidResponse()
    {
        $this->loadFixtures(
            array(
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\EventFixture',
                '\Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event\ItemFixture',
            )
        );

        $crawler = $this->client->request('GET', '/event/1/edit');
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');

        $form = $crawler->filter('#content')->selectButton('Rent')->form(
            array(
                'OktolabRentBundle_Event_Form[objects][0][scanned]' => '1',
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'Response is a redirect.');

        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response is successful.');
        $this->assertRegExp('/Event successfully rented./', $this->client->getResponse()->getContent());
    }
}
