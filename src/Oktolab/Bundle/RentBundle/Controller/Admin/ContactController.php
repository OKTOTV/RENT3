<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

/**
 * Contact controller.
 *
 * @Route("/admin/contact")
 */
class ContactController extends Controller
{

    /**
     * Lists all Contact entities.
     *
     * @Configuration\Route("/", name="admin_contact")
     * @Configuration\Method("GET")
     * @Configuration\Template()
     */
    public function indexAction()
    {
        $entities = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:Contact')->findAll();

        return array(
            'contacts' => $entities,
        );
    }

    /**
     * Finds and displays a Contact entity.
     *
     * @Configuration\Route("/{id}", name="admin_contact_show")
     * @Configuration\Method("GET")
     * @Configuration\ParamConverter("contact", class="OktolabRentBundle:Contact")
     * @Configuration\Template("OktolabRentBundle:Admin\Contact:show.html.twig", vars={"contact"})
     */
    public function showAction()
    {
    }
}
