<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment;
use Oktolab\Bundle\RentBundle\Form\Inventory\ItemType;
use Oktolab\Bundle\RentBundle\Form\Inventory\PictureType;

/**
 * Inventory\Item controller.
 *
 * @Configuration\Route("/inventory/item")
 */
class ItemController extends Controller
{

    /**
     * Lists all Inventory\Item entities.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/", name="inventory_item")
     * @Configuration\Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OktolabRentBundle:Inventory\Item')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Creates a new Inventory\Item entity.
     *
     * @Configuration\Method("POST")
     * @Configuration\Route("/", name="inventory_item_create")
     * @Configuration\Template("OktolabRentBundle:Inventory\Item:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Item();
        $form   = $this->createForm(new ItemType(), $entity);

        $form->bind($request);
        if ($form->isValid()) {
            $this->get('oktolab.upload_manager')->saveAttachmentsToEntity($entity);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $message = $this->get('translator')->trans('item.message.savesuccessful');
            $this->get('session')->getFlashBag()->add('success', $message);

            return $this->redirect($this->generateUrl('inventory_item_show', array('id' => $entity->getId())));
        }

        $message = $this->get('translator')->trans('item.message.savefailure');
        $this->get('session')->getFlashBag()->add('warning', $message);

        return array('entity' => $entity, 'form' => $form->createView());
    }

    /**
     * Displays a form to create a new Inventory\Item entity.
     * TODO: cache me
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/new", name="inventory_item_new")
     * @Configuration\Template()
     */
    public function newAction()
    {
        $entity = new Item();
        $form   = $this->createForm(
            new ItemType(),
            $entity,
            array(
                'action' => $this->generateUrl('inventory_item_create')
            )
        );

        return array('entity' => $entity, 'form' => $form->createView());
    }

    /**
     * Displays a Inventory\Item entity.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/{id}", name="inventory_item_show")
     * @Configuration\ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Configuration\Template("OktolabRentBundle:Inventory\Item:show.html.twig", vars={"item"})
     */
    public function showAction(Request $request, Item $item)
    {
        // Configuration FTW.
    }

    /**
     * Displays a form to edit an existing Inventory\Item entity.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/{id}/edit", name="inventory_item_edit")
     * @Configuration\ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Configuration\Template()
     */
    public function editAction(Item $item)
    {
        $editForm = $this->createForm(
            new ItemType(),
            $item,
            array(
                'action' => $this->generateUrl('inventory_item_update', array('id' => $item->getId())),
                'method' => 'PUT'
            )
        );

        return array('edit_form' => $editForm->createView(), 'item' => $item);
    }

    /**
     * Edits an existing Inventory\Item entity.
     *
     * @Configuration\Method("PUT")
     * @Configuration\Route("/{id}", name="inventory_item_update")
     * @Configuration\ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Configuration\Template("OktolabRentBundle:Inventory\Item:edit.html.twig")
     */
    public function updateAction(Request $request, Item $item)
    {
        $editForm = $this->createForm(new ItemType(), $item, array('method' => 'PUT'));
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $this->get('oktolab.upload_manager')->saveAttachmentsToEntity($item);

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            $message = $this->get('translator')->trans('item.message.changessuccessful');
            $this->get('session')->getFlashBag()->add('success', $message);

            return $this->redirect($this->generateUrl('inventory_item_show', array('id' => $item->getId())));
        }

        $message = $this->get('translator')->trans('item.message.changefailure');
        $this->get('session')->getFlashBag()->add('warning', $message);

        return array('item' => $item, 'edit_form' => $editForm->createView());
    }

    /**
     * Deletes a Inventory\Item entity.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/{id}/delete", name="inventory_item_delete")
     * @Configuration\ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     */
    public function deleteAction(Item $item)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);

        //TODO: create service ----------
        $fileManager = $this->get('oktolab.upload_manager');
        foreach ($item->getAttachments() as $attachment) {
            $fileManager->removeUpload($attachment);
            $em->remove($attachment);
        }

        $em->flush();

        $message = $this->get('translator')->trans('item.message.deletesuccess', array('%title%' => $item->getTitle()));
        $this->get('session')->getFlashBag()->add('success', $message);

        return $this->redirect($this->generateUrl('inventory_item'));
    }

    /**
     * Deletes an attachment from the entity
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/{entity_id}/{attachment_id}/delete", name="inventory_item_attachment_delete")
     * @Configuration\ParamConverter("item", class="OktolabRentBundle:Inventory\Item", options={"id"="entity_id"})
     * @Configuration\ParamConverter(
     *      "attachment",
     *      class="OktolabRentBundle:Inventory\Attachment",
     *      options={"id"="attachment_id"})
     */
    public function deleteAttachment(Item $item, Attachment $attachment)
    {
        $attachment === $item->getPicture() ? $item->setPicture() : $item->removeAttachment($attachment);
        $this->get('oktolab.upload_manager')->removeUpload($attachment);

        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_item_edit', array('id' => $item->getId())));
    }

    /**
     * Edit the Item Picture.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/{id}/picture/upload", name="inventory_item_picture_upload")
     * @Configuration\ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Configuration\Template("OktolabRentBundle:Inventory\Item:edit_picture.html.twig")
     */
    public function editPictureAction(Item $item)
    {
        $form = $this->createForm(
            new PictureType(),
            new Attachment(),
            array(
                'action' => $this->generateUrl('inventory_item_picture_update', array('id' => $item->getId())),
                'method' => 'POST'
            )
        );

        return array('entity' => $item, 'edit_form' => $form->createView());
    }

    /**
     * Updates the Item Picture.
     *
     * @Configuration\Method("POST")
     * @Configuration\Route("/{id}/picture/upload", name="inventory_item_picture_update")
     * @Configuration\ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     */
    public function updatePictureAction(Item $item)
    {
        $this->get('oktolab.upload_manager')->removeUpload($item->getPicture());
        $this->get('oktolab.upload_manager')->saveAttachmentsToEntity($item, true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_item_show', array('id' => $item->getId())));
    }
}
