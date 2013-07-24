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
     * Creates a new Inventory\Attachment entity.
     *
     * @Route("/", name="inventory_attachment_create")
     * @Method("POST")
     * @Template("OktolabRentBundle:Inventory\Attachment:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Attachment();
        $form = $this->createForm(new AttachmentType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $entity->upload($this->get('kernel')->getRootDir().'/../web');
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_attachment_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Inventory\Attachment entity.
     *
     * @Route("/new", name="inventory_attachment_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Attachment();
        $form   = $this->createForm(
            new AttachmentType(),
            $entity,
            array(
                'action' => $this->generateUrl('inventory_attachment_create')
            )
        );

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
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

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Inventory\Attachment entity.
     *
     * @Route("/{id}/edit", name="inventory_attachment_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OktolabRentBundle:Inventory\Attachment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventory\Attachment entity.');
        }

        $editForm = $this->createForm(new AttachmentType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Inventory\Attachment entity.
     *
     * @Route("/{id}", name="inventory_attachment_update")
     * @Method("PUT")
     * @Template("OktolabRentBundle:Inventory\Attachment:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OktolabRentBundle:Inventory\Attachment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventory\Attachment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new AttachmentType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_attachment_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Inventory\Attachment entity.
     *
     * @Route("/{id}", name="inventory_attachment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OktolabRentBundle:Inventory\Attachment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Inventory\Attachment entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('inventory_attachment'));
    }

    /**
     * Creates a form to delete a Inventory\Attachment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
