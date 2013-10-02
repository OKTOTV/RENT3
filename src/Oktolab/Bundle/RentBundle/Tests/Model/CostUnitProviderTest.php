<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Oktolab\Bundle\RentBundle\Model\CostUnitProvider;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response;

class ContactProviderTest extends WebTestCase
{
    public function testGetCostUnitsFromResource()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $SUT = static::$kernel->getContainer()->get('oktolab.costunit_provider');

        $Authresponse = new Response(200);
        $Authresponse->setBody(file_get_contents(__DIR__.'/../DataFixtures/CostUnitXml'));

        $Authplugin = new MockPlugin();
        $Authplugin->addResponse($Authresponse);
        $SUT->addSubscriber($Authplugin); //Mocks the response the auth_service gets


        $costunits = $SUT->getCostUnitsFromResource(CostUnitProvider::$Resource_HUB);

        $this->assertEquals($costunits[0]->getName(), "New Ordner");
        $this->assertEquals($costunits[0]->getAbbreviation(), "NEWO");
    }
}