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
     * @Template("OktolabRentBundle:Default:rentInventoryForm.html.twig")
     * @Cache(expires="next year", public="true")
     */
    public function rentInventoryFormAction()
    {
        $event = new Event();
        $event->setName("Michaels Test");
        $event->setBegin(new \DateTime('-3 days'));
        $event->setEnd(new \DateTime('now'));

        $eventObject = new \Oktolab\Bundle\RentBundle\Entity\EventObject();
        $eventObject->setType('Item');
        $eventObject->setObject(1);

        $event->addObject($eventObject);

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
     * @Template("OktolabRentBundle:Default:rentRoomForm.html.twig")
     * @Cache(expires="next year", public="true")
     */
    public function rentRoomFormAction()
    {
        $form = $this->createForm(new EventType(), null, array('em' => $this->getDoctrine()->getManager()));
        return array('form' => $form->createView());
    }
}
