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
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OktolabRentBundle:Inventory\Set')->findAll();

        return array(
            'entities' => $entities,
        );
    }


    /**
     * Creates a json with all items for typeahead suggestions and use
     *
     * @Route("/search.json", name="inventory_set_searchItems_json")
     * @Method("GET")
     */
    public function searchItemsAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $entities = $em->getRepository('OktolabRentBundle:Inventory\Item')->findBy(array('set' => null));
            $json = array();
            //TODO: split the descripiton to single words. That helps the typeahead to be more usefull.
            foreach ($entities as $entity) {
                $json[] = array(
                    'name' => $entity->getId(),
                    'value' => $entity->getTitle(),
                    'tokens' => array(
                        $entity->getBarcode(),
                        $entity->getDescription(),
                        $entity->getTitle()
                    )
                );
            }

            return new JsonResponse($json);
        } else {
            return $this->redirect($this->generateUrl('inventory_set'));
        }
    }

    /**
     *
     * Creates a new Inventory\Set entity.
     *
     * @Route("/", name="inventory_set_create")
     * @Method("POST")
     * @Template("OktolabRentBundle:Inventory\Set:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Set();
        $form = $this->createForm(new SetType(), $entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {

            foreach ($form->get('itemsToAdd')->getData() as $key => $value) {
                //add all items according to the id!
                $Item = $em->getRepository('OktolabRentBundle:Inventory\Item')->find($key);
                if ($Item) {
                    $Item->setSet($entity);
                    $em->persist($Item);

                } else {
                    throw $this->createNotFoundException('Unable to find Inventory\Item entity.');
                }
            }
            //TODO: move to service -------------
            $manager = $this->get('oneup_uploader.orphanage_manager')->get('gallery');
            $files = $manager->uploadFiles();

            $uploader = $this->get('oktolab.upload_manager');
            $uploader->saveAttachmentsToEntity($entity, $files);
            //-----------------------------------
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl(
                    'inventory_set_show',
                    array(
                        'id' => $entity->getId(),
                        'items' => $entity->getItems()
                    )
                )
            );
        }

        $items = array();
        foreach ($form->get('itemsToAdd')->getData() as $key => $value) {
            $items[] = $em->getRepository('OktolabRentBundle:Inventory\Item')->find($key);
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'items' => $items
        );
    }

    /**
     * Displays a form to create a new Inventory\Set entity.
     *
     * @Route("/new", name="inventory_set_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Set();
        $form   = $this->createForm(
            new SetType(),
            $entity,
            array('action' => $this->generateUrl('inventory_set_create'))
        );

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'items'  => $entity->getItems()
        );
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
     * @Template
     */
    public function editAction(Set $set)
    {
        $editForm = $this->createForm(
            new SetType(),
            $set,
            array(
                'method' => 'PUT',
                'action' => $this->generateUrl(
                    'inventory_set_update',
                    array( 'id' => $set->getId() )
                )
            )
        );
        return array(
            'set'      => $set,
            'edit_form'   => $editForm->createView(),
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
        $editForm = $this->createForm(new SetType(), $set, array('method' => 'PUT'));
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($editForm->isValid()) {

            $formItems = $editForm->get('itemsToAdd')->getData();

            //iterate the form items to update set
            foreach ($set->getItems() as $Item) {

                if (!array_key_exists($Item->getId(), $formItems)) {
                    //Item is in Database but not in form, remove it
                    $Item->setSet();
                    $em->persist($Item);
                }
                //Item is in Database and form, remove from form so you won't persist ist again
                unset($formItems[$Item->getId()]);
            }
            //formItems contains only new items. Save them
            foreach ($formItems as $key => $value) {
                $Item = $em->getRepository('OktolabRentBundle:Inventory\Item')->find($key);
                if ($Item) {
                    $Item->setSet($set);
                    $em->persist($Item);

                } else {
                    //TODO: no item found! Give the user a notice!
                }
            }

            //TODO: create service --------
            $manager = $this->get('oneup_uploader.orphanage_manager')->get('gallery');
            $files = $manager->uploadFiles();

            $uploader = $this->get('oktolab.upload_manager');
            $uploader->saveAttachmentsToEntity($set, $files);
            //-----------------------------
            $em = $this->getDoctrine()->getManager();
            $em->persist($set);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_set_show', array('id' => $set->getId())));
        }

        return array(
            'set'      => $set,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Inventory\Set entity.
     *
     * @Route("/delete/{id}", name="inventory_set_delete")
     * @ParamConverter("set", class="OktolabRentBundle:Inventory\Set")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction(Set $set)
    {
        foreach ($set->getItems() as $Item) {
            $Item->setSet();
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($set);

        //TODO: create service --------
        $fileManager = $this->get('oktolab.upload_manager');
        foreach ($set->getAttachments() as $attachment) {
            $fileManager->removeUpload($attachment);
            $em->remove($attachment);
        }
        //-----------------------------

        $em->flush();
        return $this->redirect($this->generateUrl('inventory_set'));
    }

    /**
     * Remove an Item from a set entity.
     *
     * @Route("{setid}/remove/item/{id}", name="inventory_set_remove_item")
     * @ParamConverter("set", class="OktolabRentBundle:Inventory\Set", options={"id" = "setid"})
     * @ParamConverter("item", class="OktolabRentBundle:Inventory\Item", options={"id" = "id"})
     * @Method("GET")
     */
    public function removeItemAction(Item $item, Set $set)
    {
        $item->setSet();
        $em->flush($item);

        //TODO: redirect back to Set edit.
        $this->get('session')->getFlashBag()->add(
            'notice',
            sprintf('Item %s wurde erfolgreich aus Set entfernt.', $item->getTitle())
        );
        return $this->redirect($this->generateUrl('inventory_set_edit', array('id' => $set->getId())));
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
        if ($attachment === $set->getPicture()) {
            $set->setPicture();
        } else {
            $set->removeAttachment($attachment);
        }

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
        $picture = new Attachment();
        $form   = $this->createForm(
            new PictureType(),
            $picture,
            array(
                'action' => $this->generateUrl('inventory_set_picture_update', array('id' => $set->getId())),
                'method' => 'PUT'
                )
        );

        return array(
            'entity' => $set,
            'edit_form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/picture/upload", name="inventory_set_picture_update")
     * @ParamConverter("set", class="OktolabRentBundle:Inventory\Set")
     * @Method("PUT")
     */
    public function updatePictureAction(Set $set)
    {
        //TODO: move to service? -------
        $manager = $this->get('oneup_uploader.orphanage_manager')->get('gallery');
        $files = $manager->uploadFiles();

        $uploader = $this->get('oktolab.upload_manager');
        $uploader->saveAttachmentsToEntity($set, $files, true);
        //-----------------------------

        $em = $this->getDoctrine()->getManager();
        $em->persist($set);
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_set_show', array('id' => $set->getId())));
    }
}
