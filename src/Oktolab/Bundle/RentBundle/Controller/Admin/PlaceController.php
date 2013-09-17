<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
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
        $form = $this->createForm(new PlaceType(), $entity = new Place());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('message.place.createSuccessful', array('%placeTitle%' => $entity->getTitle())));
            return $this->redirect($this->generateUrl('inventory_place'));
        }

        $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('message.place.createFailure'));
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
     * @Cache(expires="next year")
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
                'action' => $this->generateUrl('inventory_place_update', array('id' => $place->getId())),
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
        $editForm = $this->createForm(
            new PlaceType(),
            $place,
            array(
                'action' => $this->generateUrl('inventory_place_update', array('id' => $place->getId())),
                'method' => 'PUT',
            )
        );

        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('message.place.changeSuccessful', array('%placeTitle%' => $place->getTitle())));
            return $this->redirect($this->generateUrl('inventory_place'));
        }

        $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('message.place.changeFailure'));
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
                'error',
                $this->get('translator')->trans(
                    'message.place.deleteFailure',
                    array('%placeTitle%' => $place->getTitle())
                )
            );
            return $this->redirect($this->generateUrl('inventory_place'));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($place);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans(
                'message.place.deleteSuccessful',
                array('%placeTitle%' => $place->getTitle())
            )
        );
        return $this->redirect($this->generateUrl('inventory_place'));
    }
}
