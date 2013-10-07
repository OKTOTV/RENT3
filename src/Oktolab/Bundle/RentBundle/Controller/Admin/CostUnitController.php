<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;
use Oktolab\Bundle\RentBundle\Form\CostUnitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * CostUnit controller.
 *
 * @Route("/admin/costunit")
 */
class CostUnitController extends Controller
{

    /**
     * Lists all CostUnit entities.
     *
     * @Route("/", name="admin_costunit")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OktolabRentBundle:CostUnit')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new CostUnit entity.
     *
     * @Route("/", name="admin_costunit_create")
     * @Method("POST")
     * @Template("OktolabRentBundle:Admin\CostUnit:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new CostUnit();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($form->get('contacts')->getData() as $contact) {
                $contact->setCostunit($entity);
                $em->persist($contact);
            }
            $entity->setContacts($form->get('contacts')->getData());

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_costunit_show', array('id' => $entity->getId())));
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a CostUnit entity.
    *
    * @param CostUnit $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(CostUnit $entity)
    {
        $form = $this->createForm(new CostUnitType(), $entity, array(
            'hubTransformer' => $this->get('oktolab.hub_guid_contact_transformer'),
            'action' => $this->generateUrl('admin_costunit_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new CostUnit entity.
     *
     * @Route("/new", name="admin_costunit_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CostUnit();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a CostUnit entity.
     *
     * @Route("/{id}", name="admin_costunit_show")
     * @ParamConverter("costunit", class="OktolabRentBundle:CostUnit")
     * @Method("GET")
     * @Template()
     */
    public function showAction(CostUnit $costunit)
    {
        return array('costunit' => $costunit);
    }

    /**
     * Displays a form to edit an existing CostUnit entity.
     *
     * @Route("/{id}/edit", name="admin_costunit_edit")
     * @ParamConverter("costunit", class="OktolabRentBundle:CostUnit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(CostUnit $costunit)
    {
        $editForm = $this->createEditForm($costunit);

        return array(
            'costunit'    => $costunit,
            'form'   => $editForm->createView()
        );
    }

    /**
    * Creates a form to edit a CostUnit entity.
    *
    * @param CostUnit $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CostUnit $entity)
    {
        $form = $this->createForm(new CostUnitType(), $entity, array(
            'hubTransformer' => $this->get('oktolab.hub_guid_contact_transformer'),
            'action' => $this->generateUrl('admin_costunit_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing CostUnit entity.
     *
     * @Route("/{id}", name="admin_costunit_update")
     * @Method("PUT")
     * @ParamConverter("costunit", class="OktolabRentBundle:CostUnit")
     * @Template("OktolabRentBundle:CostUnit:edit.html.twig")
     */
    public function updateAction(Request $request, CostUnit $costunit)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($costunit);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $contacts = $editForm->get('contacts')->getData();

            foreach ($contacts as $contact) {
                $contact->setCostUnit($costunit);
                $em->persist($contact);
            }
            $costunit->setContacts($contacts);

            $em->persist($costunit);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_costunit_show', array('id' => $costunit->getId())));
        }

        return array(
            'entity'      => $costunit,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Deletes a CostUnit entity.
     *
     * @Route("/{id}", name="admin_costunit_delete")
     * @ParamConverter("costunit", class="OktolabRentBundle:CostUnit")
     * @Method("GET")
     */
    public function deleteAction(CostUnit $costunit)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $em->remove($costunit);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_costunit'));
    }
}
