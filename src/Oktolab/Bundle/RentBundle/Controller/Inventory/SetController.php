<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
use Oktolab\Bundle\RentBundle\Form\Inventory\SetType;
use Symfony\Component\HttpFoundation\JsonResponse;

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

     * @Route("/search.{_format}",
     *      name="inventory_set_searchItems_json",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function searchItemsAction(Request $request)
    {
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
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OktolabRentBundle:Inventory\Set')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventory\Set entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'items'       => $entity->getItems(),
        );
    }

    /**
     * Displays a form to edit an existing Inventory\Set entity.
     *
     * @Route("/{id}/edit", name="inventory_set_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OktolabRentBundle:Inventory\Set')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventory\Set entity.');
        }

        $editForm = $this->createForm(
            new SetType(),
            $entity,
            array(
                'method' => 'PUT',
                'action' => $this->generateUrl(
                    'inventory_set_update',
                    array( 'id' => $id )
                )
            )
        );

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'items'       => $entity->getItems()
        );
    }

    /**
     * Edits an existing Inventory\Set entity.
     *
     * @Route("/{id}", name="inventory_set_update")
     * @Method("PUT")
     * @Template("OktolabRentBundle:Inventory\Set:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OktolabRentBundle:Inventory\Set')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventory\Set entity.');
        }
        $items = $entity->getItems();
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SetType(), $entity, array('method' => 'PUT'));
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $formItems = $editForm->get('itemsToAdd')->getData();

            //iterate the form items to update set
            foreach ($entity->getItems() as $Item) {

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
                    $Item->setSet($entity);
                    $em->persist($Item);

                } else {
                    //TODO: no item found! Give the user a notice!
                }
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_set_show', array('id' => $id, 'items' => $items)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'items'       => $items
        );
    }

    /**
     * Deletes a Inventory\Set entity.
     *
     * @Route("/{id}/delete", name="inventory_set_delete")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OktolabRentBundle:Inventory\Set')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventory\Set entity.');
        }

        foreach ($entity->getItems() as $Item) {
            $Item->setSet();
        }

        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_set'));
    }

    /**
     * Remove an Item from a set entity.
     *
     * @Route("/{setid}/remove/item/{id}", name="inventory_set_remove_item")
     * @Method("GET")
     */
    public function removeItemAction($id, $setid)
    {
        $em = $this->getDoctrine()->getManager();

        $item = $em->getRepository('OktolabRentBundle:Inventory\Item')->find($id);
        $set = $em->getRepository('OktolabRentBundle:Inventory\Set')->find($setid);
        if (!$item) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Dieses Item konnte nicht gefunden werden.'
            );
        } else {
            $item->setSet();
            $em->flush($item);

            //TODO: redirect back to Set edit.
            $this->get('session')->getFlashBag()->add(
                'notice',
                sprintf('Item %s wurde erfolgreich aus Set entfernt.', $item->getTitle())
            );
        }
        if (!$set) {
            return $this->redirect($this->generateUrl('inventory_set'));
        }
        return $this->redirect($this->generateUrl('inventory_set_edit', array('id' => $setid)));
    }

    /**
     * Creates a form to delete a Inventory\Set entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
}
