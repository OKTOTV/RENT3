<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Oktolab\Bundle\RentBundle\Entity\CostUnit;
use Guzzle\Http\Client;
use Doctrine\ORM\EntityManager;


class CostUnitProvider extends Client
{

    public static $Resource_HUB=0;
    public static $Resource_RENT=1;

    private $entityManager;

    public function __construct(EntityManager $manager, $baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);
        $this->entityManager = $manager;
    }

    public function getCostUnitsFromResource($resource=0)
    {
        switch ($resource) {
            case CostUnitProvider::$Resource_HUB:
                return $this->getCostUnitsFromHub();
                break;
            case CostUnitProvider::$Resource_RENT:
                return $this->entityManager->getRepository('OktolabRentBundle:CostUnit')->findAll();
                break;
        }
    }

    public function addCostUnitsToRent($costunits, $flush=true)
    {
        foreach ($costunits as $costunit) {
            $this->entityManager->persist($costunit);
        }
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    private function getCostUnitsFromHub()
    {
        $response = $this->get()->send();
        $seriess = simplexml_load_string($response->getBody(true));
        $costunits = array();

        foreach($seriess as $series) {
            $costunit = new CostUnit();
            $costunit->setName((string)$series->title);
            $costunit->setAbbreviation((string)$series->abbrevation);
            $costunits[] = $costunit;
        }
        return $costunits;
    }
}