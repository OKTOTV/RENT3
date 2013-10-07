<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Oktolab\Bundle\RentBundle\Entity\CostUnit;
use Guzzle\Http\Client;
use Doctrine\ORM\EntityManager;
use Oktolab\Bundle\RentBundle\Model\HubFetchService;


class CostUnitProvider extends Client
{

    public static $Resource_HUB=0;
    public static $Resource_RENT=1;

    private $entityManager;
    private $hubFetchService;

    public function __construct(EntityManager $manager, HubFetchService $hubFetchService, $baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);
        $this->entityManager = $manager;
        $this->hubFetchService = $hubFetchService;
    }

    public function getCostUnitsFromResource($resource=0)
    {
        switch ($resource) {
            case CostUnitProvider::$Resource_HUB:
                return $this->fetchCostUnitsFromHub();
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

    private function getDataFromHub()
    {
        $response = $this->get()->send();
        $seriess = simplexml_load_string($response->getBody(true));
        $costunits = array();
        foreach($seriess as $series) {
            $costUnit = new CostUnit();
            $costUnit->setGuid((string)$series->abbrevation);
            $costUnit->setName((string)$series->title);
            $costunits[] = $costUnit;
        }
        return $costunits;
    }

    private function fetchCostUnitsFromHub()
    {
        $costUnits = $this->getDataFromHub();
        foreach($costUnits as $costUnit) {
            $costunit[] = $this->getAdditionalDataByFetch($costUnit);
        }

        return $costUnits;
    }

    /**
     * Tries to get additionalInfo (GUID) from Hub.
     */
    private function getAdditionalDataByFetch($costUnit)
    {
        if ($extendedCostUnit = $this->hubFetchService->getExtendedCostUnitByFetch($costUnit)) {
            return $extendedCostUnit;
        }
        return $costUnit;
    }
}