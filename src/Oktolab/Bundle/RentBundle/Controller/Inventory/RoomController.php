<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Room;
use Oktolab\Bundle\RentBundle\Form\Inventory\RoomType;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment;
use Oktolab\Bundle\RentBundle\Form\Inventory\PictureType;

/**
 * Inventory\Room controller.
 *
 * @Route("/inventory/room")
 */
class RoomController extends Controller
{

    /**
     * Lists all Inventory\Room entities.
     *
     * @Route("/", name="inventory_room")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OktolabRentBundle:Inventory\Room')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Inventory\Room entity.
     *
     * @Route("/", name="inventory_room_create")
     * @Method("POST")
     * @Template("OktolabRentBundle:Inventory\Room:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Room();
        $form = $this->createForm(new RoomType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $this->get('oktolab.upload_manager')->saveAttachmentsToEntity($entity);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this
                ->get('session')
                ->getFlashBag()
                ->add(
                    'success',
                    $this->get('translator')->trans('room.message.saveSuccessful', array('%roomTitle%' => $entity->getTitle()))
                );

            return $this->redirect($this->generateUrl('inventory_room_show', array('id' => $entity->getId())));
        }

        $this
                ->get('session')
                ->getFlashBag()
                ->add(
                    'warning',
                    $this->get('translator')->trans('room.message.saveFailure')
                );

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Inventory\Room entity.
     *
     * @Route("/new", name="inventory_room_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Room();
        $form   = $this->createForm(
            new RoomType(),
            $entity,
            array('action' => $this->generateUrl('inventory_room_create'))
        );

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Inventory\Room entity.
     *
     * @Route("/{id}", name="inventory_room_show")
     * @ParamConverter("room", class="OktolabRentBundle:Inventory\Room")
     * @Method("GET")
     * @Template("OktolabRentBundle:Inventory\Room:show.html.twig", vars={"room"})
     */
    public function showAction($id)
    {
    }

    /**
     * Displays a form to edit an existing Inventory\Room entity.
     *
     * @Route("/{id}/edit", name="inventory_room_edit")
     * @ParamConverter("room", class="OktolabRentBundle:Inventory\Room")
     * @Method("GET")
     * @Template
     */
    public function editAction(Room $room)
    {
        $editForm = $this->createForm(
            new RoomType(),
            $room,
            array(
                'method' => 'PUT',
                'action' => $this->generateUrl(
                    'inventory_room_update',
                    array( 'id' => $room->getId() )
                )
            )
        );

        return array(
            'entity'      => $room,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Edits an existing Inventory\Room entity.
     *
     * @Route("/{id}", name="inventory_room_update")
     * @ParamConverter("room", class="OktolabRentBundle:Inventory\Room")
     * @Method("PUT")
     * @Template("OktolabRentBundle:Inventory\Room:edit.html.twig")
     */
    public function updateAction(Request $request, Room $room)
    {
        $editForm = $this->createForm(new RoomType(), $room);
        $editForm->bind($request);

        if ($editForm->isValid()) {

            $this->get('oktolab.upload_manager')->saveAttachmentsToEntity($room);

            $em = $this->getDoctrine()->getManager();
            $em->persist($room);
            $em->flush();

            $this
                ->get('session')
                ->getFlashBag()
                ->add(
                    'success',
                    $this->get('translator')->trans('room.message.changeSuccessful', array('%roomTitle%' => $room->getTitle()))
                );

            return $this->redirect($this->generateUrl('inventory_room_show', array('id' => $room->getId())));
        }

        $this
                ->get('session')
                ->getFlashBag()
                ->add(
                    'warning',
                    $this->get('translator')->trans('room.message.changeFailure')
                );

        return array(
            'entity'      => $room,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Inventory\Room entity.
     *
     * @Route("/{id}/delete", name="inventory_room_delete")
     * @ParamConverter("room", class="OktolabRentBundle:Inventory\Room")
     * @Method("GET")
     */
    public function deleteAction(Room $room)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($room);

        //TODO: create service --------
        $fileManager = $this->get('oktolab.upload_manager');
        foreach ($room->getAttachments() as $attachment) {
            $fileManager->removeUpload($attachment);
            $em->remove($attachment);
        }
        //-----------------------------

        $this
                ->get('session')
                ->getFlashBag()
                ->add(
                    'success',
                    $this->get('translator')->trans('room.message.deleteSuccess', array('%roomTitle%' => $room->getTitle()))
                );

        $em->flush();
        return $this->redirect($this->generateUrl('inventory_room'));
    }

    /**
     * Deletes an attachment from the entity
     *
     * @Route("/{entity_id}/{attachment_id}/delete", name="inventory_room_attachment_delete")
     * @ParamConverter("room", class="OktolabRentBundle:Inventory\Room", options={"id" = "entity_id"})
     * @ParamConverter("attachment", class="OktolabRentBundle:Inventory\Attachment", options={"id" = "attachment_id"})
     * @Method("GET")
     */
    public function deleteAttachment(Room $room, Attachment $attachment)
    {
        $fileManager = $this->get('oktolab.upload_manager');
        if ($attachment === $room->getPicture()) {
            $room->setPicture();
        } else {
            $room->removeAttachment($attachment);
        }

        $fileManager->removeUpload($attachment);

        $em = $this->getDoctrine()->getManager();
        $em->persist($room);
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_room_edit', array('id' => $room->getId())));
    }

    /**
     * @Route("/{id}/picture/upload", name="inventory_room_picture_upload")
     * @ParamConverter("room", class="OktolabRentBundle:Inventory\Room")
     * @Method("GET")
     * @Template("OktolabRentBundle:Inventory\Room:edit_picture.html.twig")
     */
    public function uploadPictureAction(Room $room)
    {
        $picture = new Attachment();
        $form   = $this->createForm(
            new PictureType(),
            $picture,
            array(
                'action' => $this->generateUrl('inventory_room_picture_update', array('id' => $room->getId())),
                'method' => 'POST'
                )
        );

        return array(
            'entity' => $room,
            'edit_form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/picture/upload", name="inventory_room_picture_update")
     * @ParamConverter("room", class="OktolabRentBundle:Inventory\Room")
     * @Method("POST")
     */
    public function updatePictureAction(Room $room)
    {
        $this->get('oktolab.upload_manager')->removeUpload($room->getPicture());
        $this->get('oktolab.upload_manager')->saveAttachmentsToEntity($room, true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($room);
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_room_show', array('id' => $room->getId())));
    }
}
