<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment;
use Oktolab\Bundle\RentBundle\Form\Inventory\AttachmentType;

/**
 * Inventory\Attachment controller.
 *
 * @Route("/inventory/attachment")
 */
class AttachmentController extends Controller
{

    /**
     * Lists all Inventory\Attachment entities.
     *
     * @Route("/", name="inventory_attachment")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OktolabRentBundle:Inventory\Attachment')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Inventory\Attachment entity.
     *
     * @Route("/{id}", name="inventory_attachment_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OktolabRentBundle:Inventory\Attachment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventory\Attachment entity.');
        }

        return array(
            'entity' => $entity
        );
    }
}
