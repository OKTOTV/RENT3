<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment;
use Oktolab\Bundle\RentBundle\Form\Inventory\ItemType;

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
            //TODO: create service --------
            $manager = $this->get('oneup_uploader.orphanage_manager')->get('gallery');
            $files = $manager->uploadFiles();

            $uploader = $this->get('oktolab.upload_manager');
            $uploader->saveAttachmentsToEntity($entity, $files);
            // ----------------------------

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
     * @Method("GET")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OktolabRentBundle:Inventory\Item')->find($id);

        if ($request->isXmlHttpRequest()) {
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Inventory\Item entity.');
            } else {
                return $this->render(
                    'OktolabRentBundle:Inventory\Item:row.html.twig',
                    array('entity' => $entity)
                );
            }
        } else {

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Inventory\Item entity.');
            }

            return array(
                'entity' => $entity,
            );
        }
    }

    /**
     * Displays a form to edit an existing Inventory\Item entity.
     *
     * @Route("/{id}/edit", name="inventory_item_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OktolabRentBundle:Inventory\Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventory\Item entity.');
        }

        $editForm = $this->createForm(
            new ItemType(),
            $entity,
            array(
                'action' => $this->generateUrl(
                    'inventory_item_update',
                    array( 'id' => $id )
                ),
                'method' => 'PUT'
            )
        );

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Inventory\Item entity.
     *
     * @Route("/{id}", name="inventory_item_update")
     * @Method("PUT")
     * @Template("OktolabRentBundle:Inventory\Item:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OktolabRentBundle:Inventory\Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventory\Item entity.');
        }

        $editForm = $this->createForm(new ItemType(), $entity, array('method' => 'PUT'));
        $editForm->bind($request);

        if ($editForm->isValid()) {

            $manager = $this->get('oneup_uploader.orphanage_manager')->get('gallery');
            $files = $manager->uploadFiles();

            $uploader = $this->get('oktolab.upload_manager');
            $uploader->saveAttachmentsToEntity($entity, $files);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_item_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Inventory\Item entity.
     *
     * @Route("/delete/{id}", name="inventory_item_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OktolabRentBundle:Inventory\Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventory\Item entity.');
        }
        //TODO: create service ----------
        $fileManager = $this->get('oktolab.upload_manager');

        $em->remove($entity);
        foreach ($entity->getAttachments() as $attachment) {
            $fileManager->removeUpload($attachment);
            $em->remove($attachment);
        }
        //-------------------------------
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_item'));
    }
}
