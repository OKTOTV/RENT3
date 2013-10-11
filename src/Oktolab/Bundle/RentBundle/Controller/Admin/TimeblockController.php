<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Entity\Timeblock;
use Oktolab\Bundle\RentBundle\Form\TimeblockType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Timeblock controller.
 *
 * @Route("/admin/timeblock")
 */
class TimeblockController extends Controller
{

    /**
     * Lists all Timeblock entities.
     *
     * @Route("/", name="admin_timeblock")
     * @Method("GET")
     * @Template()
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
     * @Route("/", name="admin_timeblock_create")
     * @Method("POST")
     * @Template("OktolabRentBundle:Timeblock:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Timeblock();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            //die(var_dump($entity));
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

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
     * @Route("/new", name="admin_timeblock_new")
     * @Method("GET")
     * @Template()
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
     * @Route("/{id}", name="admin_timeblock_show")
     * @ParamConverter("timeblock", class="OktolabRentBundle:Timeblock")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Timeblock $timeblock)
    {
        return array('timeblock' => $timeblock);
    }

    /**
     * Displays a form to edit an existing Timeblock entity.
     *
     * @Route("/{id}/edit", name="admin_timeblock_edit")
     * @ParamConverter("timeblock", class="OktolabRentBundle:Timeblock")
     * @Method("GET")
     * @Template()
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
     * @Route("/{id}", name="admin_timeblock_update")
     * @Method("PUT")
     * @ParamConverter("timeblock", class="OktolabRentBundle:Timeblock")
     * @Template("OktolabRentBundle:Timeblock:edit.html.twig")
     */
    public function updateAction(Request $request, Timeblock $timeblock)
    {
        $editForm = $this->createEditForm($timeblock);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($timeblock);
            $em->flush();

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
     * @Route("/{id}", name="admin_timeblock_delete")
     * @ParamConverter("timeblock", class="OktolabRentBundle:Timeblock")
     * @Method("GET")
     */
    public function deleteAction(Timeblock $timeblock)
    {
        //TODO: check constrains
        $em->remove($timeblock);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_timeblock'));
    }
}
