<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment;
use Oktolab\Bundle\RentBundle\Form\Inventory\ItemType;
use Oktolab\Bundle\RentBundle\Form\Inventory\PictureType;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Qms;
use Oktolab\Bundle\RentBundle\Form\QMSType;
use Oktolab\Bundle\RentBundle\Form\ItemQMSType;

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
     * @Configuration\Route("s/{page}/{toDisplay}",
     *      name="inventory_item",
     *      defaults={"page"=1, "toDisplay"=10},
     *      requirements={"page"="\d+"})
     *
     * @Configuration\Template()
     *
     * @param int $page         current page to display
     * @param int $toDisplay    how many items per page
     *
     * @return array
     */
    public function indexAction($page, $toDisplay)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:Inventory\Item');

        $count = $repository->fetchAllCount();
        $items = $repository->getAllJoinSetAndJoinCategoryQuery()
            ->setFirstResult(($page - 1) * $toDisplay)
            ->setMaxResults($toDisplay)
            ->getResult();

        return array('entities' => $items, 'nbPages' => ceil($count / $toDisplay), 'currentPage' => $page);
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

            $this->get('session')->getFlashBag()->add('success', 'item.message.savesuccessful');

            return $this->redirect($this->generateUrl('inventory_item_show', array('id' => $entity->getId())));
        }

        $this->get('session')->getFlashBag()->add('warning', 'item.message.savefailure');
        return array('entity' => $entity, 'form' => $form->createView());
    }

    /**
     * Displays a form to create a new Inventory\Item entity.
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
     * @Configuration\Template("OktolabRentBundle:Inventory\Item:show.html.twig")
     */
    public function showAction(Item $item)
    {
        $events = array();
        $eventObjects = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:EventObject')->findBy(array('object'=> $item->getId(), 'type' => $item->getType()));
        foreach ($eventObjects as $eventObject) {
            $events[] = $eventObject->getEvent();
        }
        return array('item' => $item, 'events' => array_reverse($events));
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

        return array('form' => $editForm->createView(), 'item' => $item);
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

            $this->get('session')->getFlashBag()->add('success', 'item.message.changessuccessful');
            return $this->redirect($this->generateUrl('inventory_item_show', array('id' => $item->getId())));
        }

        $this->get('session')->getFlashBag()->add('warning', 'item.message.changefailure');

        return array('item' => $item, 'form' => $editForm->createView());
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

    /**
     * Form to change current qms of an Item.
     *
     * @Configuration\Method({"GET", "POST"})
     * @Configuration\Route("/{id}/new_status", name="inventory_item_create_qms")
     * @Configuration\ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Configuration\Template("OktolabRentBundle:Inventory\Item:create_qms.html.twig")
     */
    public function createQmsAction(Request $request, Item $item)
    {
        $states = array(
            QMS::STATE_DAMAGED,
            QMS::STATE_DESTROYED,
            QMS::STATE_LOST,
            QMS::STATE_MAINTENANCE,
            Qms::STATE_DISCARDED
        );

        $qms = new Qms();
        $qms->setStatus(Qms::STATE_DAMAGED);
        $qms->setItem($item);
        $form = $this->createForm(
            new QMSType($states),
            $qms,
            array(
                'action' => $this->generateUrl('inventory_item_create_qms', array('id' => $item->getId())),
                'method' => 'POST'
                )
            )
            ->add('save', 'submit');

        if ($request->getMethod() == "GET") { // wants form
            return array('form' => $form->createView());
        } else { // posts form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $message = $this->get('translator')->trans('inventory.item.add_qms_success');
                $this->get('session')->getFlashBag()->add('success', $message);
                $this->get('oktolab.qms')->createQMS($qms);
                return $this->redirect($this->generateUrl('inventory_item_show', array('id' => $item->getId())));
            }
            $message = $this->get('translator')->trans('inventory.item.add_qms_error');
            $this->get('session')->getFlashBag()->add('error', $message);
            return array('form' => $form->createView());
        }
    }

    /**
     * Form to change old qmss and give the item a new state
     * This form should be used when the item comes back from maintenance or inactivity.
     * @Configuration\Method({"GET", "POST"})
     * @Configuration\Route("/{id}/change_status", name="inventory_item_change_qms")
     * @Configuration\ParamConverter("item", class="OktolabRentBundle:Inventory\Item")
     * @Configuration\Template("OktolabRentBundle:Inventory\Item:change_qms.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Item $item
     */
    public function changeQmsAction(Request $request, Item $item)
    {
        $form = $this->createForm(
            new ItemQMSType(),
            $item,
            array(
                'action' => $this->generateUrl('inventory_item_change_qms', array('id' => $item->getId())),
                'method' => 'POST'
                )
            )
            ->add('save', 'submit');

        if ($request->getMethod() == "GET") { // wants form
            return array('form' => $form->createView());
        } else { // posts form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $message = $this->get('translator')->trans('inventory.item.add_qms_success');
                $this->get('session')->getFlashBag()->add('success', $message);

                $qms = $form['qms']->getData();
                $qms->setItem($item);
                $this->get('oktolab.qms')->createQMS($qms);

                $em = $this->getDoctrine()->getManager();
                $em->persist($item);
                $em->flush();

                return $this->redirect($this->generateUrl('inventory_item_show', array('id' => $item->getId())));
            }
            $message = $this->get('translator')->trans('inventory.item.add_qms_error');
            $this->get('session')->getFlashBag()->add('error', $message);
            return array('form' => $form->createView());
        }
    }
}
