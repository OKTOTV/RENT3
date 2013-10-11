<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Entity\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Route("/", name="admin_contact")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OktolabRentBundle:Contact')->findAll();

        return array(
            'contacts' => $entities,
        );
    }

    /**
     * Finds and displays a Contact entity.
     *
     * @Route("/{id}", name="admin_contact_show")
     * @Method("GET")
     * @ParamConverter("contact", class="OktolabRentBundle:Contact")
     * @Template()
     */
    public function showAction(Contact $contact)
    {
        return array(
            'contact'      => $contact,
        );
    }
}
