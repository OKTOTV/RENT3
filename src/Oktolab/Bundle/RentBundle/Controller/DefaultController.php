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
            'roomEvents'      => $eventRepository->findActiveFromBeginToEnd($begin, $end, 'Room'),
            'inventoryEvents' => $eventRepository->findActiveFromBeginToEnd($begin, $end, 'Inventory'),
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
