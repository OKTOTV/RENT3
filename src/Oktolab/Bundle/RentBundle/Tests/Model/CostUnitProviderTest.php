<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Oktolab\Bundle\RentBundle\Model\CostUnitProvider;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response;

class CostUnitProviderTest extends WebTestCase
{
    public function testGetCostUnitsFromResource()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $SUT = static::$kernel->getContainer()->get('oktolab.costunit_provider');

        $SUTfetcher = static::$kernel->getContainer()->get('oktolab.hub_fetch_service'); //costunit_provider uses this.
        $fetchResponse = new Response(200);
        $fetchResponse->setBody('0');
        $fetchPlugin = new MockPlugin();
        $fetchPlugin->addResponse($fetchResponse);
        $SUTfetcher->addSubscriber($fetchPlugin);

        $Authresponse = new Response(200);
        $Authresponse->setBody(file_get_contents(__DIR__.'/../DataFixtures/CostUnitXml'));

        $Authplugin = new MockPlugin();
        $Authplugin->addResponse($Authresponse);
        $SUT->addSubscriber($Authplugin); //Mocks the response the auth_service gets


        $costunits = $SUT->getCostUnitsFromResource(CostUnitProvider::$Resource_HUB);

        $this->assertEquals($costunits[0]->getName(), "New Ordner");
        $this->assertEquals($costunits[0]->getGuid(), "NEWO");
    }

    public function testAddCostUnitsToRent()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $SUT = static::$kernel->getContainer()->get('oktolab.costunit_provider');

        $SUTfetcher = static::$kernel->getContainer()->get('oktolab.hub_fetch_service'); //costunit_provider uses this.
        $fetchResponse = new Response(200);
        $fetchResponse->setBody('0');
        $fetchPlugin = new MockPlugin();
        $fetchPlugin->addResponse($fetchResponse);
        $SUTfetcher->addSubscriber($fetchPlugin);

        $costUnitresponse = new Response(200);
        $costUnitresponse->setBody(file_get_contents(__DIR__.'/../DataFixtures/CostUnitXml'));

        $responsePlugin = new MockPlugin();
        $responsePlugin->addResponse($costUnitresponse);
        $SUT->addSubscriber($responsePlugin); //Mocks the response the auth_service gets

        $costunits = $SUT->getCostUnitsFromResource(CostUnitProvider::$Resource_HUB);

        $SUT->addCostUnitsToRent($costunits);
        $entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $rentCostUnits = $entityManager->getRepository('OktolabRentBundle:CostUnit')->findAll();

        $this->assertEquals(count($rentCostUnits), 1);
    }
}