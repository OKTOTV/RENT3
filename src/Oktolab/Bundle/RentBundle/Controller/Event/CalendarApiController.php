<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

/**
 * @Configuration\Route("/api/calendar")
 */
class CalendarApiController extends Controller
{

    /**
     * Returns JSON formatted inventory.
     *
     * @Configuration\Cache(expires="+5 min", public="yes")
     * @Configuration\Method("GET")
     * @Configuration\Route("/inventory.{_format}",
     *      name="OktolabRentBundle_CalendarApi_Inventory",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function inventoryAction()
    {
        $inventory = $this->get('oktolab.event_calendar_inventory')->getTransformedInventory(true);

        return new JsonResponse($inventory);
    }

    /**
     * Returns JSON formatted Timeblocks.
     *
     * @Configuration\Cache(expires="+5 min", public="yes")
     * @Configuration\Method("GET")
     * @Configuration\Route("/timeblock.{_format}/{begin}/{end}",
     *      name="OktolabRentBundle_CalendarApi_Timeblock",
     *      defaults={"_format"="json", "begin" = "default", "end" = "default"},
     *      requirements={"_format"="json"})
     *
     * @Configuration\ParamConverter("begin",
     *      converter="oktolab.datetime_converter",
     *      options={"default": "today 00:00"})
     *
     * @Configuration\ParamConverter("end",
     *      converter="oktolab.datetime_converter",
     *      options={"default": "+7 days 00:00"})
     *
     * @return JsonResponse
     */
    public function timeblockAction(\DateTime $begin, \DateTime $end)
    {
        $timeblocks = $this->get('oktolab.event_calendar_timeblock')->getTransformedTimeblocks($begin, $end);

        return new JsonResponse($timeblocks);
    }

    /**
     * Returns JSON formatted Events.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/events.{_format}/{begin}/{end}",
     *      name="OktolabRentBundle_CalendarApi_Event",
     *      defaults={"_format"="json", "begin" = "default", "end" = "default"},
     *      requirements={"_format"="json|html"})
     *
     * @Configuration\ParamConverter("begin", converter="oktolab.datetime_converter", options={"default": "now"})
     * @Configuration\ParamConverter("end", converter="oktolab.datetime_converter", options={"default": "+7 Days"})
     *
     * @return JsonResponse
     */
    public function eventAction(\DateTime $begin, \DateTime $end)
    {
        $events = $this->get('oktolab.event_calendar_event')->getFormattedActiveEvents($begin, $end);

        return new JsonResponse($events);
    }

    /**
     * Returns JSON formatted inventory.
     *
     * @Configuration\Cache(expires="+5 min", public="yes")
     * @Configuration\Method("GET")
     * @Configuration\Route("/rooms.{_format}",
     *      name="orb_calendar_api_rooms",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function RoomAction()
    {
        $rooms = $this->get('oktolab.room_api_service')->getRoomsForCalendar();
        return new JsonResponse($rooms);
    }

    /**
     * Returns JSON formatted Timeblocks for rooms
     *
     * @Configuration\Cache(expires="+5 min", public="yes")
     * @Configuration\Method("GET")
     * @Configuration\Route("/room_timeblock.{_format}/{begin}/{end}",
     *      name="orb_calendar_api_room_timeblocks",
     *      defaults={"_format"="json", "begin" = "default", "end" = "default"},
     *      requirements={"_format"="json"})
     *
     * @Configuration\ParamConverter("begin",
     *      converter="oktolab.datetime_converter",
     *      options={"default": "today 00:00"})
     *
     * @Configuration\ParamConverter("end",
     *      converter="oktolab.datetime_converter",
     *      options={"default": "+7 days 00:00"})
     *
     * @return JsonResponse
     */
    public function RoomTimeblockAction(\DateTime $begin, \DateTime $end)
    {
        $timeblocks = $this->get('oktolab.event_calendar_timeblock')->getTransformedTimeblocks($begin, $end, null, 'room');

        return new JsonResponse($timeblocks);
    }

    /** Returns JSON formatted Timeblocks for rooms
     *
     * @Configuration\Cache(expires="+5 min", public="yes")
     * @Configuration\Method("GET")
     * @Configuration\Route("/room_day_timeblock.{_format}/{begin}/{end}",
     *      name="orb_calendar_api_room_day_timeblocks",
     *      defaults={"_format"="json", "begin" = "default", "end" = "default"},
     *      requirements={"_format"="json"})
     *
     * @Configuration\ParamConverter("begin",
     *      converter="oktolab.datetime_converter",
     *      options={"default": "today 00:00"})
     *
     * @Configuration\ParamConverter("end",
     *      converter="oktolab.datetime_converter",
     *      options={"default": "today 23:59"})
     *
     * @return JsonResponse
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     * @return JsonResponse
     */
    public function RoomDayTimeblockAction(\DateTime $begin, \DateTime $end)
    {
        $timeblocks = $this->get('oktolab.event_calendar_timeblock')->getDayTimeblocks($begin, $end, 'room');
        return new JsonResponse($timeblocks);
    }

    /**
     * Returns JSON formatted Events.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/room_events.{_format}/{begin}/{end}",
     *      name="orb_room_api_events",
     *      defaults={"_format"="json", "begin" = "default", "end" = "default"},
     *      requirements={"_format"="json|html"})
     *
     * @Configuration\ParamConverter("begin",
     *      converter="oktolab.datetime_converter",
     *      options={"default": "today 00:00"})
     *
     * @Configuration\ParamConverter("end",
     *      converter="oktolab.datetime_converter",
     *      options={"default": "+7 days 00:00"})
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @return JsonResponse
     */
    public function RoomEventAction(\DateTime $begin, \DateTime $end)
    {
        $events = $this->get('oktolab.event_calendar_event')->getFormattedActiveEvents($begin, $end, 'room');

        return new JsonResponse($events);
    }
}
