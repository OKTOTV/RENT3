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
     * Displays Inventory/Room Events for the next 7 days.
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
        $end = new \DateTime('+7 Days');

        return array(
            'roomEvents'      => $eventRepository->findActiveFromBeginToEnd($begin, $end, 'room'),
            'inventoryEvents' => $eventRepository->findActiveFromBeginToEnd($begin, $end, 'inventory'),
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
