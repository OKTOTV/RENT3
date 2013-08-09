<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
use Oktolab\Bundle\RentBundle\Form\Inventory\SetType;

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

        return array('entities' => $entities);
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
        $set = new Set();
        $form = $this->createForm(new SetType(), $set);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($form->get('items')->getData() as $item) {
                $item->setSet($set);
                $em->persist($item);
            }

            $em->persist($set);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_set_show', array('id' => $set->getId())));
        }

        return array(
            'form'   => $form->createView(),
            'items'  => $form->get('items')->getData(),
        );
    }

    /**
     * Displays a form to create a new Inventory\Set entity.
     *
     * @Route("/new", name="inventory_set_new")
     * @Method("GET")
     * @Cache(expires="next year", public="true")
     * @Template()
     */
    public function newAction()
    {
        $form = $this->createForm(
            new SetType(),
            new Set(),
            array('action' => $this->generateUrl('inventory_set_create'))
        );

        return array('form' => $form->createView());
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

        if (!$set = $em->getRepository('OktolabRentBundle:Inventory\Set')->find($id)) {
            throw $this->createNotFoundException('Unable to find Inventory\Set entity.');
        }

        $form = $this->createForm(
            new SetType(),
            $set,
            array(
                'method' => 'PUT',
                'action' => $this->generateUrl('inventory_set_update', array('id' => $set->getId())),
            )
        );

        return array(
            'entity' => $set,
            'form'   => $form->createView(),
            'items'  => $set->getItems(),
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
        if (!$set = $em->getRepository('OktolabRentBundle:Inventory\Set')->find($id)) {
            throw $this->createNotFoundException('Unable to find Inventory\Set entity.');
        }

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
            $formItems = $form->get('items')->getData();
            foreach ($set->getItems() as $item) {
                if (!array_key_exists($item->getId(), $formItems)) {
                    $item->setSet(null);
                    $em->persist($item);
                }
            }

            foreach ($formItems as $item) {
                $item->setSet($set);
                $em->persist($item);
            }

            $em->persist($set);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_set_show', array('id' => $id)));
        }

        return array(
            'entity' => $set,
            'form'   => $form->createView(),
            'items'  => $set->getItems(),
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

        $this->get('logger')->debug('TODO: Set message to avoid @$!#* with users');

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
