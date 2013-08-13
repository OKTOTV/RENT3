<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment;
use Oktolab\Bundle\RentBundle\Form\Inventory\ItemType;
use Oktolab\Bundle\RentBundle\Form\Inventory\PictureType;

/**
 * Inventory\Item controller.
 *
 * @Route("/inventory/item")
 */
class ItemController extends Controller
{

    /**
     * Lists all Inventory\Item entities.
     *
     * @Route("/", name="inventory_item")
     * @Method("GET")
     * @Cache(expires="+1 day", public="true")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OktolabRentBundle:Inventory\Item')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Inventory\Item entity.
     *
     * @Route("/", name="inventory_item_create")
     * @Method("POST")
     * @Template("OktolabRentBundle:Inventory\Item:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Item();
        $form = $this->createForm(new ItemType(), $entity);
        $form->bind($request);
        if ($form->isValid()) {
            $this->get('oktolab.upload_manager')->saveAttachmentsToEntity($entity);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_item_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Inventory\Item entity.
     *
     * @Route("/new", name="inventory_item_new")
     * @Method("GET")
     * @Template()
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

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Inventory\Item entity. If XMLHTTP Request, the Item will be displayed as tablerow.
     * Is used in Sets for adding new items.
     *
     * @Route("/{id}", name="inventory_item_show")
     * @ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Method("GET")
     * @Template("OktolabRentBundle:Inventory\Item:show.html.twig", vars={"item"})
     */
    public function showAction(Request $request, Item $item)
    {
        if ($request->isXmlHttpRequest()) {
                return $this->render(
                    'OktolabRentBundle:Inventory\Item:row.html.twig',
                    array('entity' => $item)
                );
        }
    }

    /**
     * Displays a form to edit an existing Inventory\Item entity.
     *
     * @Route("/{id}/edit", name="inventory_item_edit")
     * @ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Method("GET")
     * @Template
     */
    public function editAction(Item $item)
    {
        $editForm = $this->createForm(
            new ItemType(),
            $item,
            array(
                'action' => $this->generateUrl(
                    'inventory_item_update',
                    array( 'id' => $item->getId() )
                ),
                'method' => 'PUT'
            )
        );

        return array('edit_form' => $editForm->createView(), 'item' => $item);
    }

    /**
     * Edits an existing Inventory\Item entity.
     *
     * @Route("/{id}", name="inventory_item_update")
     * @ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Method("PUT")
     * @Template("OktolabRentBundle:Inventory\Item:edit.html.twig")
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

            return $this->redirect($this->generateUrl('inventory_item_show', array('id' => $item->getId())));
        }

        return array(
            'item'      => $item,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Inventory\Item entity.
     * @Route("/{id}/delete", name="inventory_item_delete")
     * @ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Method("GET")
     */
    public function deleteAction($item)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);

        //TODO: create service ----------
        $fileManager = $this->get('oktolab.upload_manager');
        foreach ($item->getAttachments() as $attachment) {
            $fileManager->removeUpload($attachment);
            $em->remove($attachment);
        }
        //-------------------------------
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_item'));
    }

    /**
     * Deletes an attachment from the entity
     *
     * @Route("/{entity_id}/{attachment_id}/delete", name="inventory_item_attachment_delete")
     * @ParamConverter("item", class="OktolabRentBundle:Inventory\Item", options={"id" = "entity_id"})
     * @ParamConverter("attachment", class="OktolabRentBundle:Inventory\Attachment", options={"id" = "attachment_id"})
     * @Method("GET")
     */
    public function deleteAttachment(Item $item, Attachment $attachment)
    {
        $fileManager = $this->get('oktolab.upload_manager');
        if ($attachment === $item->getPicture()) {
            $item->setPicture();
        } else {
            $item->removeAttachment($attachment);
        }

        $fileManager->removeUpload($attachment);

        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_item_edit', array('id' => $item->getId())));
    }

    /**
     * @Route("/{id}/picture/upload", name="inventory_item_picture_upload")
     * @ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Method("GET")
     * @Template("OktolabRentBundle:Inventory\Item:edit_picture.html.twig")
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

        return array(
            'entity' => $item,
            'edit_form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/picture/upload", name="inventory_item_picture_update")
     * @ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Method("POST")
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
