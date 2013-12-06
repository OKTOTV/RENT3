<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Oktolab\Bundle\RentBundle\Entity\Timeblock;
use Oktolab\Bundle\RentBundle\Form\TimeblockType;

/**
 * Timeblock controller.
 *
 * @Configuration\Route("/admin/timeblock")
 */
class TimeblockController extends Controller
{

    /**
     * Lists all Timeblock entities.
     *
     * @Configuration\Route("/", name="admin_timeblock")
     * @Configuration\Method("GET")
     * @Configuration\Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OktolabRentBundle:Timeblock')->findAll();

        return array(
            'timeblocks' => $entities,
        );
    }

    /**
     * Creates a new Timeblock entity.
     *
     * @Configuration\Route("/", name="admin_timeblock_create")
     * @Configuration\Method("POST")
     * @Configuration\Template("OktolabRentBundle:Timeblock:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Timeblock();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'admin.timeblock.create_success');
            return $this->redirect($this->generateUrl('admin_timeblock_show', array('id' => $entity->getId())));
        }
        return array(
            'timeblock' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Timeblock entity.
    *
    * @param Timeblock $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Timeblock $entity)
    {
        $form = $this->createForm(new TimeblockType(), $entity, array(
            'translator' => $this->get('translator'),
            'action' => $this->generateUrl('admin_timeblock_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Timeblock entity.
     *
     * @Configuration\Route("/new", name="admin_timeblock_new")
     * @Configuration\Method("GET")
     * @Configuration\Template()
     */
    public function newAction()
    {
        $timeblock = new Timeblock();
        $form   = $this->createCreateForm($timeblock);

        return array(
            'timeblock' => $timeblock,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Timeblock entity.
     *
     * @Configuration\Route("/{id}", name="admin_timeblock_show")
     * @Configuration\ParamConverter("timeblock", class="OktolabRentBundle:Timeblock")
     * @Configuration\Method("GET")
     * @Configuration\Template()
     */
    public function showAction(Timeblock $timeblock)
    {
        return array('timeblock' => $timeblock);
    }

    /**
     * Displays a form to edit an existing Timeblock entity.
     *
     * @Configuration\Route("/{id}/edit", name="admin_timeblock_edit")
     * @Configuration\ParamConverter("timeblock", class="OktolabRentBundle:Timeblock")
     * @Configuration\Method("GET")
     * @Configuration\Template()
     */
    public function editAction(Timeblock $timeblock)
    {
        $editForm = $this->createEditForm($timeblock);

        return array(
            'timeblock'      => $timeblock,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
    * Creates a form to edit a Timeblock entity.
    *
    * @param Timeblock $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Timeblock $entity)
    {
        $form = $this->createForm(new TimeblockType(), $entity, array(
            'translator' => $this->get('translator'),
            'action' => $this->generateUrl('admin_timeblock_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }

    /**
     * Edits an existing Timeblock entity.
     *
     * @Configuration\Route("/{id}", name="admin_timeblock_update")
     * @Configuration\Method("PUT")
     * @Configuration\ParamConverter("timeblock", class="OktolabRentBundle:Timeblock")
     * @Configuration\Template("OktolabRentBundle:Timeblock:edit.html.twig")
     */
    public function updateAction(Request $request, Timeblock $timeblock)
    {
        $editForm = $this->createEditForm($timeblock);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($timeblock);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'admin.timeblock.save_success');
            return $this->redirect($this->generateUrl('admin_timeblock_show', array('id' => $timeblock->getId())));
        }

        return array(
            'timeblock'      => $timeblock,
            'edit_form'   => $editForm->createView()
        );
    }
    /**
     * Deletes a Timeblock entity.
     *
     * @Configuration\Route("/{id}/delete", name="admin_timeblock_delete")
     * @Configuration\ParamConverter("timeblock", class="OktolabRentBundle:Timeblock")
     * @Configuration\Method("GET")
     */
    public function deleteAction(Timeblock $timeblock)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($timeblock);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'admin.timeblock.delete_success');
        return $this->redirect($this->generateUrl('admin_timeblock'));
    }
}
