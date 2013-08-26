<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;
use Oktolab\Bundle\RentBundle\Form\Inventory\PlaceType;

/**
 * Inventory\Place controller.
 *
 * @Route("/admin/inventory/place")
 */
class PlaceController extends Controller
{

    /**
     * Lists all Inventory\Place entities.
     *
     * @Route("/", name="inventory_place")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OktolabRentBundle:Inventory\Place')->findAll();
        return array('entities' => $entities);
    }

    /**
     * Creates a new Inventory\Place entity.
     *
     * @Route("/", name="inventory_place_create")
     * @Method("POST")
     * @Template("OktolabRentBundle:Admin\Place:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Place();
        $form = $this->createForm(new PlaceType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_place_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Inventory\Place entity.
     *
     * @Route("/new", name="inventory_place_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $form = $this->createForm(
            new PlaceType(),
            new Place(),
            array('action' => $this->generateUrl('inventory_place_create'))
        );

        return array('form' => $form->createView());
    }

    /**
     * Finds and displays a Inventory\Place entity.
     *
     * @Route("/{id}", name="inventory_place_show")
     * @ParamConverter("place", class="OktolabRentBundle:Inventory\Place")
     * @Template("OktolabRentBundle:Admin\Place:show.html.twig", vars={"place"})
     * @Method("GET")
     */
    public function showAction(Place $place)
    {
    }

    /**
     * Displays a form to edit an existing Inventory\Place entity.
     *
     * @Route("/{id}/edit", name="inventory_place_edit")
     * @ParamConverter("place", class="OktolabRentBundle:Inventory\Place")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Place $place)
    {
        $editForm = $this->createForm(
            new PlaceType(),
            $place,
            array(
                'action' => $this->generateUrl(
                    'inventory_place_update',
                    array('id' => $place->getId())
                ),
                'method' => 'PUT',
            )
        );

        return array(
            'place'     => $place,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Inventory\Place entity.
     *
     * @Route("/{id}", name="inventory_place_update")
     * @ParamConverter("place", class="OktolabRentBundle:Inventory\Place")
     * @Method("PUT")
     * @Template("OktolabRentBundle:Admin\Place:edit.html.twig")
     */
    public function updateAction(Request $request, Place $place)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(new PlaceType(), $place);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($place);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_place_show', array('id' => $place->getId())));
        }

        return array(
            'entity'      => $place,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Inventory\Place entity.
     *
     * @Route("/{id}/delete", name="inventory_place_delete")
     * @ParamConverter("place", class="OktolabRentBundle:Inventory\Place")
     * @Method("GET")
     */
    public function deleteAction(Place $place)
    {
        if ($place->getItems()->count() != 0 || $place->getSets()->count() != 0) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Kann nicht gelöscht werden! Besitzt noch Gegenstände!'
            );
            return $this->redirect($this->generateUrl('inventory_place_edit', array('id' => $place->getId())));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($place);
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_place'));
    }
}
