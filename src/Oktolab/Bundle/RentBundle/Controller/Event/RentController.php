<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Form\EventType;

/**
 * Rent Controller.
 *
 * @Route("/rent")
 */
class RentController extends Controller
{

    /**
     * @Route("/inventory", name="rentbundle_create_rent_inventory")
     * @Template("OktolabRentBundle:Event:rentInventoryForm.html.twig")
     * @Cache(expires="next year", public="true")
     */
    public function rentInventoryFormAction()
    {
        $event = new Event();
        $event->setName("Michaels Test");
        $event->setBegin(new \DateTime('-3 days'));
        $event->setEnd(new \DateTime('now'));

        $form = $this->createForm(
            new EventType(),
            $event,
            array(
                'action' => $this->generateUrl('event_create'),
                'method' => 'POST',
                'em'     => $this->getDoctrine()->getManager(),
            )
        );

        return array('form' => $form->createView());
    }

    /**
     * @Route("/room", name="rentbundle_create_rent_room")
     * @Template("OktolabRentBundle:Event:rentRoomForm.html.twig")
     * @Cache(expires="next year", public="true")
     */
    public function rentRoomFormAction()
    {
        $form = $this->createForm(new EventType(), null, array('em' => $this->getDoctrine()->getManager()));

        return array('form' => $form->createView());
    }
}
