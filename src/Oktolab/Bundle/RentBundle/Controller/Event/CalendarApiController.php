<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/api/calendar")
 */
class CalendarApiController extends Controller
{
    /**
     * @Route("/", name="OktolabRentBundle_EventApi_Index")
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Cache(expires="+1 day", public="yes")
     * @Method("GET")
     * @Route("/inventory.{_format}",
     *      name="OktolabRentBundle_CalendarApi_Inventory",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function inventoryAction()
    {
        $inventory = $this->get('oktolab.event_calendar_inventory')->getTransformedInventory();
        return new JsonResponse($inventory);
    }

    /**
     * @Cache(expires="+1 day", public="yes")
     * @Method("GET")
     * @Route("/timeblock.{_format}",
     *      name="OktolabRentBundle_CalendarApi_Timeblock",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function timeblockAction()
    {
        $timeblocks = $this->get('oktolab.event_calendar_timeblock')
            ->getTransformedTimeblocks(new \DateTime('today 00:00'), new \DateTime('+30 days 00:00'));
        return new JsonResponse($timeblocks);
    }
}
