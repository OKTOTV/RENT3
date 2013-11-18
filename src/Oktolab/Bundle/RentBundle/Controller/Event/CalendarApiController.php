<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Oktolab\Bundle\RentBundle\Util\Configuration\DateTimeParamConverter;

/**
 * @Route("/api/calendar")
 */
class CalendarApiController extends Controller
{

    /**
     * @Cache(expires="+5 min", public="yes")
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
        $inventory = $this->get('oktolab.event_calendar_inventory')->getTransformedInventory(true);
        return new JsonResponse($inventory);
    }

    /**
     * @Cache(expires="+5 min", public="yes")
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

    /**
     * //Cache(expires="+5 min", public="yes")
     * @Method("GET")
     * @Route("/events.{_format}/{begin}/{end}",
     *      name="OktolabRentBundle_CalendarApi_Event",
     *      defaults={"_format"="json", "begin" = "default", "end" = "default"},
     *      requirements={"_format"="json|html"})
     * @Configuration\ParamConverter("begin", converter="oktolab.datetime_converter", options={"default": "now"})
     * @Configuration\ParamConverter("end", converter="oktolab.datetime_converter", options={"default": "+30 Days"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function eventAction(\DateTime $begin, \DateTime $end)
    {
        $events = $this->get('oktolab.event_calendar_event')->getFormattedActiveEvents($begin, $end);
        return new JsonResponse($events);
    }
}
