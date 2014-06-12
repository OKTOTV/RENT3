<?php

namespace Oktolab\Bundle\RentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * DefaultController.
 */
class DefaultController extends Controller
{
    /**
     * Displays Inventory/Room Events for the next days.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/", name="rentbundle_dashboard")
     * @Configuration\Template()
     *
     * @return array
     */
    public function dashboardAction()
    {
        $eventRepository = $this->get('oktolab.event_manager')->getEventRepository();

        $begin = new \DateTime();
        $begin->setTime(0, 0);
        $end = new \DateTime('+3 Days');
        $end->setTime(23, 59);

        $roomEvents = $eventRepository->findActiveFromBeginToEnd($begin, $end, 'room');
        $roomObjects = array();
        foreach ($roomEvents as $roomEvent) {
            $roomObjects[] = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($roomEvent->getObjects()); 
        }

        $inventoryEvents = $eventRepository->findActiveFromBeginToEnd($begin, $end, 'inventory');
        $inventoryObjects = array();
        foreach ($inventoryEvents as $inventoryEvent) {
            $inventoryObjects[] = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($inventoryEvent->getObjects()); 
        }

        return array(
            'roomEvents'      => $roomEvents,
            'roomObjects'     => $roomObjects,
            'inventoryEvents' => $inventoryEvents,
            'inventoryObjects'=> $inventoryObjects,
            'begin'           => $begin,
            'end'             => $end
        );
    }

    /**
     * Displays Inventory/Room Events for the next days.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/dashboard/{begin}/{end}",
     *      name="rentbundle_dashboard_specific",
     *      defaults={"begin" = "default", "end" = "default"})
     * @Configuration\ParamConverter("begin",
     *      converter="oktolab.datetime_converter",
     *      options={"default": "today 00:00"})
     *
     * @Configuration\ParamConverter("end",
     *      converter="oktolab.datetime_converter",
     *      options={"default": "+3 days 00:00"})
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     * @Configuration\Template("OktolabRentBundle:Default:dashboard.html.twig")
     *
     * @return array
     */
    public function dashboardBrowseAction(\DateTime $begin, \DateTime $end)
    {
        $eventRepository = $this->get('oktolab.event_manager')->getEventRepository();

        $roomEvents = $eventRepository->findActiveFromBeginToEnd($begin, $end, 'room');
        $roomObjects = array();
        foreach ($roomEvents as $roomEvent) {
            $roomObjects[] = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($roomEvent->getObjects()); 
        }

        $inventoryEvents = $eventRepository->findActiveFromBeginToEnd($begin, $end, 'inventory');
        $inventoryObjects = array();
        foreach ($inventoryEvents as $inventoryEvent) {
            $inventoryObjects[] = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($inventoryEvent->getObjects()); 
        }

        return array(
            'roomEvents'      => $roomEvents,
            'roomObjects'     => $roomObjects,
            'inventoryEvents' => $inventoryEvents,
            'inventoryObjects'=> $inventoryObjects,
            'begin'           => $begin,
            'end'             => $end
        );
    }

    /*
     * Shows overdue events.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/overdue_events", name="rentbundle_overdue_events")
     * @Configuration\Template()
     *
     * @return array
     */
    public function overdueAction()
    {
        $inventoryEvents = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:Event')->getOverduedEvents('inventory');
        $roomEvents = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:Event')->getOverduedEvents('room');


        return array(
                'inventoryEvents' =>$inventoryEvents,
                'roomEvents' =>$roomEvents
            );
    }

    /**
     * Renders OktolabCalendar.js.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/calendar", name="rentbundle_calendar")
     * @Configuration\Template()
     *
     * @return array
     */
    public function calendarAction()
    {
        return array();
    }

    /**
     * Renders day_room_calendar.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/room_day_calendar", name="orb_room_day_calendar")
     * @Configuration\Template()
     *
     * @return array
     */
    public function roomDayCalendarAction()
    {
        return array();
    }

    /**
     * Renders day_inventory_calendar.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/inventory_day_calendar", name="orb_inventory_day_calendar")
     * @Configuration\Template()
     *
     * @return array
     */
    public function inventoryDayCalendarAction()
    {
        return array();
    }

    /**
     * Renders OktolabRoomCalendar.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/room_calendar", name="orb_room_calendar")
     * @Configuration\Template()
     *
     * @return array
     */
    public function roomCalendarAction()
    {
        return array();
    }

    /**
     * Displays about page.
     *
     * @Configuration\Cache(expires="next year", public="true")
     * @Configuration\Method("GET")
     * @Configuration\Route("/about", name="rentbundle_about")
     * @Configuration\Template()
     *
     * @return array
     */
    public function aboutAction()
    {
        $raw = explode("\n###\n", file_get_contents($this->get('kernel')->getRootDir().'/../LICENSE'));
        $licenses = array();
        foreach ($raw as $i) {
            $header = strtok($i, "\n");
            $licenses[trim($header)] = str_replace($header, '', $i);
        }

        return array('licenses' => $licenses);
    }
}
