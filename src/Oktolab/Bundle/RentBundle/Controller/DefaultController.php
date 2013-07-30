<?php

namespace Oktolab\Bundle\RentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Cache(expires="next year", public="true")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="rentbundle_dashboard")
     * @Cache(expires="23 hours", public="true")
     * @Template()
     */
    public function dashboardAction()
    {
        return array();
    }

    /**
     * @Route("/about", name="rentbundle_about")
     * @Template()
     */
    public function aboutAction()
    {
        return array('licenses' => file_get_contents($this->get('kernel')->getRootDir().'/../LICENSE'));
    }

    /**
     * @Route("/rent/inventory", name="rentbundle_create_rent_inventory")
     * @Template("OktolabRentBundle:Default:rentInventoryForm.html.twig")
     */
    public function rentInventoryFormAction()
    {
        $form = $this->createForm(new \Oktolab\Bundle\RentBundle\Form\EventType());
        return array('form' => $form->createView());
    }

    /**
     * @Route("/rent/room", name="rentbundle_create_rent_room")
     * @Template("OktolabRentBundle:Default:rentRoomForm.html.twig")
     */
    public function rentRoomFormAction()
    {
        $form = $this->createForm(new \Oktolab\Bundle\RentBundle\Form\EventType());
        return array('form' => $form->createView());
    }
}
