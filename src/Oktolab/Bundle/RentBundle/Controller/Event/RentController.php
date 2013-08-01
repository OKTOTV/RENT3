<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

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
        $form = $this->createForm(
            new \Oktolab\Bundle\RentBundle\Form\EventType(),
            null,
            array(
                'action' => $this->generateUrl(
                    'event_create'
                ),
                'method' => 'POST'
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
        $form = $this->createForm(new \Oktolab\Bundle\RentBundle\Form\EventType());
        return array('form' => $form->createView());
    }
}
