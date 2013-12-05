<?php

namespace Oktolab\Bundle\RentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * DefaultController.
 */
class DefaultController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/", name="rentbundle_dashboard")
     * @Template()
     */
    public function dashboardAction()
    {
        $eventRepository = $this->get('oktolab.event_manager')->getEventRepository();
        $roomEvents = $eventRepository->findActiveFromBeginToEnd(new \DateTime(), new \DateTime('+7 Days'), 'Room');
        $inventoryEvents = $eventRepository->findActiveFromBeginToEnd(new \DateTime(), new \DateTime('+7 Days'), 'Inventory');

        return array(
            'roomEvents' => $roomEvents,
            'inventoryEvents' => $inventoryEvents
        );
    }

    /**
     * @Method("GET")
     * @Route("/calendar", name="rentbundle_calendar")
     * @Template()
     */
    public function calendarAction()
    {
        return array();
    }

    /**
     * @Cache(expires="next year", public="true")
     * @Method("GET")
     * @Route("/about", name="rentbundle_about")
     * @Template()
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
