<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
use Oktolab\Bundle\RentBundle\Form\Inventory\SetType;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment;
use Oktolab\Bundle\RentBundle\Form\Inventory\PictureType;

/**
 * Inventory\Set controller.
 *
 * @Route("/inventory/set")
 */
class SetController extends Controller
{
    /**
     * Lists all Inventory\Set entities.
     *
     * @Route("/", name="inventory_set")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $entities = $this->getDoctrine()
            ->getManager()
            ->getRepository('OktolabRentBundle:Inventory\Set')
            ->findAll();

        return array('entities' => $entities);
    }

    /**
     * Creates a new Inventory\Set entity.
     *
     * @Route("/", name="inventory_set_create")
     * @Method("POST")
     * @Template("OktolabRentBundle:Inventory\Set:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $set = new Set();
        $form = $this->createForm(new SetType(), $set);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($form->get('items')->getData() as $item) {
                $item->setSet($set);
                $em->persist($item);
            }

            $this->get('oktolab.upload_manager')->saveAttachmentsToEntity($set);
            $em->persist($set);
            $em->flush();

            $message = $this->get('translator')->trans('set.message.savesuccessful');
            $this->get('session')->getFlashBag()->add('success', $message);

            return $this->redirect($this->generateUrl('inventory_set_show', array('id' => $set->getId())));
        }

        $message = $this->get('translator')->trans('set.message.savefailure');
        $this->get('session')->getFlashBag()->add('warning', $message);

        return array(
            'form'   => $form->createView(),
            'items'  => $form->get('items')->getData(),
        );
    }

    /**
     * Displays a form to create a new Inventory\Set entity.
     *
     * @Method("GET")
     * @Route("/new", name="inventory_set_new")
     * @Template()
     */
    public function newAction()
    {
        $form = $this->createForm(
            new SetType(),
            new Set(),
            array(
                'method' => 'POST',
                'action' => $this->generateUrl('inventory_set_create')
            )
        );

        return array('form' => $form->createView());
    }

    /**
     * Finds and displays a Inventory\Set entity.
     *
     * @Route("/{id}", name="inventory_set_show")
     * @ParamConverter("set", class="OktolabRentBundle:Inventory\Set")
     * @Method("GET")
     * @Template("OktolabRentBundle:Inventory\Set:show.html.twig", vars={"set"})
     */
    public function showAction(Set $set)
    {
    }

    /**
     * Displays a form to edit an existing Inventory\Set entity.
     *
     * @Route("/{id}/edit", name="inventory_set_edit")
     * @ParamConverter("set", class="OktolabRentBundle:Inventory\Set")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Set $set)
    {
        $form = $this->createForm(
            new SetType(),
            $set,
            array(
                'method' => 'PUT',
                'action' => $this->generateUrl('inventory_set_update', array('id' => $set->getId())),
            )
        );

        return array(
            'set'    => $set,
            'form'   => $form->createView(),
            'items'  => $set->getItems(),
        );
    }

    /**
     * Edits an existing Inventory\Set entity.
     *
     * @Route("/{id}", name="inventory_set_update")
     * @ParamConverter("set", class="OktolabRentBundle:Inventory\Set")
     * @Method("PUT")
     * @Template("OktolabRentBundle:Inventory\Set:edit.html.twig")
     */
    public function updateAction(Request $request, Set $set)
    {
        $form = $this->createForm(
            new SetType(),
            $set,
            array(
                'method' => 'PUT',
                'action' => $this->generateUrl('inventory_set_update', array('id' => $set->getId())),
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //remove set_id from items
            $items = $em->getRepository('OktolabRentBundle:Inventory\Item')->findBy(array('set' => $set->getId()));
            foreach ($items as $item) {
                $item->setSet(null);
            }

            //add all items from form to set
            foreach ($set->getItems() as $item) {
                $item->setSet($set);
                $em->persist($item);
            }

            $em->persist($set);
            $em->flush();

            $message = $this->get('translator')->trans('set.message.changesuccessful');
            $this->get('session')->getFlashBag()->add('success', $message);

            return $this->redirect($this->generateUrl('inventory_set_show', array('id' => $set->getId())));
        }

        $message = $this->get('translator')->trans('set.message.changefailure');
        $this->get('session')->getFlashBag()->add('warning', $message);

        return array(
            'set'    => $set,
            'form'   => $form->createView(),
            'items'  => $set->getItems(),
        );
    }

    /**
     * Deletes a Inventory\Set entity.
     *
     * @Route("/{id}/delete", name="inventory_set_delete")
     * @ParamConverter("set", class="OktolabRentBundle:Inventory\Set")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction(Set $set)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($set->getItems() as $item) {
            $item->setSet(null);
            $em->persist($item);
        }

        $em->remove($set);

        //TODO: create service --------
        $fileManager = $this->get('oktolab.upload_manager');
        foreach ($set->getAttachments() as $attachment) {
            $fileManager->removeUpload($attachment);
            $em->remove($attachment);
        }
        //-----------------------------

        $em->flush();
        $this
            ->get('session')
            ->getFlashBag()
            ->add(
                'success',
                $this->get('translator')->trans('set.message.deletesuccess', array('%title%' => $set->getTitle()))
            );
        return $this->redirect($this->generateUrl('inventory_set'));
    }

    /**
     * Deletes an attachment from the entity
     *
     * @Route("/{entity_id}/{attachment_id}/delete", name="inventory_set_attachment_delete")
     * @ParamConverter("set", class="OktolabRentBundle:Inventory\Set", options={"id" = "entity_id"})
     * @ParamConverter("attachment", class="OktolabRentBundle:Inventory\Attachment", options={"id" = "attachment_id"})
     * @Method("GET")
     */
    public function deleteAttachment(Set $set, Attachment $attachment)
    {
        $fileManager = $this->get('oktolab.upload_manager');
        ($attachment === $set->getPicture()) ? $set->setPicture(null) : $set->removeAttachment($attachment);

        $fileManager->removeUpload($attachment);

        $em = $this->getDoctrine()->getManager();
        $em->persist($set);
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_set_edit', array('id' => $set->getId())));
    }

    /**
     * @Route("/{id}/picture/upload", name="inventory_set_picture_upload")
     * @ParamConverter("set", class="OktolabRentBundle:Inventory\Set")
     * @Method("GET")
     * @Template("OktolabRentBundle:Inventory\Set:edit_picture.html.twig")
     */
    public function uploadPictureAction(Set $set)
    {
        $form = $this->createForm(
            new PictureType(),
            new Attachment(),
            array(
                'action' => $this->generateUrl('inventory_set_picture_update', array('id' => $set->getId())),
                'method' => 'POST',
            )
        );

        return array(
            'entity'    => $set,
            'edit_form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/picture/upload", name="inventory_set_picture_update")
     * @ParamConverter("set", class="OktolabRentBundle:Inventory\Set")
     * @Method("POST")
     */
    public function updatePictureAction(Set $set)
    {
        $this->get('oktolab.upload_manager')->removeUpload($set->getPicture());
        $this->get('oktolab.upload_manager')->saveAttachmentsToEntity($set, true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($set);
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_set_show', array('id' => $set->getId())));
    }
}
