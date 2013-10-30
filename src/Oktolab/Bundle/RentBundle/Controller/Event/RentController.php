<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Form\EventType;

/**
 * Rent Controller.
 *
 * @Configuration\Route("/rent")
 */
class RentController extends Controller
{

    /**
     * Returns a new Inventory EventForm.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/inventory", name="rentbundle_create_rent_inventory")
     * @Configuration\Template("OktolabRentBundle:Event:rentInventoryForm.html.twig")
     * @Configuration\Cache(expires="next year", public="true")
     *
     * @return array
     */
    public function rentInventoryFormAction()
    {
        $form = $this->get('form.factory')->create(
            'OktolabRentBundle_Event_Form',
            new Event(),
            array(
                'action' => $this->generateUrl('OktolabRentBundle_Event_Create'),
                'method' => 'POST',
                'em'     => $this->getDoctrine()->getManager(),
            )
        );

        $form->remove('cancel');
        $form->remove('delete');
        $form->remove('rent');

        return array('form' => $form->createView());
    }

    /**
     * Returns a new Room EventForm.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/room", name="rentbundle_create_rent_room")
     * @Configuration\Template("OktolabRentBundle:Event:rentRoomForm.html.twig")
     * @Configuration\Cache(expires="next year", public="true")
     *
     * @return array
     */
    public function rentRoomFormAction()
    {
        $form = $this->createForm(new EventType(), null, array('em' => $this->getDoctrine()->getManager()));

        return array('form' => $form->createView());
    }
}
