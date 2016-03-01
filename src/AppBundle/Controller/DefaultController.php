<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $outgoing = $em->getRepository('AppBundle:Event')->findOutgoingEvents();
        $incoming = $em->getRepository('AppBundle:Event')->findIncomingEvents();

        return ['outgoing' => $outgoing, 'incoming' => $incoming];
    }

    /**
     * @Route("start/{start}/end/{end}", name="homepage_timerange")
     * @ParamConverter("start", options={"format": "Y-m-d"})
     * @ParamConverter("end", options={"format": "Y-m-d"})
     * @Template()
     */
    public function indexSpecificAction(\Datetime $start, \Datetime $end)
    {
        $em = $this->getDoctrine()->getManager();
        $outgoing = $em->getRepository('AppBundle:Event')->findOutgoingEvents($start, $end);
        $incoming = $em->getRepository('AppBundle:Event')->findIncomingEvents($start, $end);

        return ['outgoing' => $outgoing, 'incoming' => $incoming];
    }

    /**
     * @Route("/calendar/{start}/{end}", name="calendar")
     * @Template()
     */
    public function calendarAction($start, $end)
    {
        return ['start' => $start, 'end' => $end];
    }
}
