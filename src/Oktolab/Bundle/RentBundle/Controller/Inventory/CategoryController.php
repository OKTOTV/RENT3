<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Category;
use Oktolab\Bundle\RentBundle\Form\Inventory\CategoryType;

/**
 * Inventory\Category controller.
 *
 * @Route("/admin/inventory/category")
 */
class CategoryController extends Controller
{

    /**
     * Lists all Inventory\Category entities.
     *
     * @Route("/", name="inventory_category")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OktolabRentBundle:Inventory\Category')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Inventory\Category entity.
     *
     * @Route("/", name="inventory_category_create")
     * @Method("POST")
     * @Template("OktolabRentBundle:Inventory\Category:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Category();
        $form = $this->createForm(new CategoryType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_category_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Inventory\Category entity.
     *
     * @Route("/new", name="inventory_category_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $form = $this->createForm(
            new CategoryType(),
            new Category(),
            array('action' => $this->generateUrl('inventory_category_create'))
        );

        return array(
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Inventory\Category entity.
     *
     * @Route("/{id}", name="inventory_category_show")
     * @ParamConverter("category", class="OktolabRentBundle:Inventory\Category")
     * @Method("GET")
     * @Template("OktolabRentBundle:Inventory\Category:show.html.twig", vars={"category"})
     */
    public function showAction(Category $category)
    {
    }

    /**
     * Displays a form to edit an existing Inventory\Category entity.
     *
     * @Route("/{id}/edit", name="inventory_category_edit")
     * @ParamConverter("category", class="OktolabRentBundle:Inventory\Category")
     * @Method("GET")
     * @Template
     */
    public function editAction(Category $category)
    {
        $editForm = $this->createForm(
            new CategoryType(),
            $category,
            array(
                'method' => 'PUT',
                'action' => $this->generateUrl('inventory_category_update', array('id' => $category->getId()))
            )
        );

        return array(
            'category'  => $category,
            'edit_form' => $editForm->createView()
        );
    }

    /**
     * Edits an existing Inventory\Category entity.
     *
     * @Route("/{id}", name="inventory_category_update")
     * @ParamConverter("category", class="OktolabRentBundle:Inventory\Category")
     * @Method("PUT")
     * @Template("OktolabRentBundle:Inventory\Category:edit.html.twig")
     */
    public function updateAction(Request $request, Category $category)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(new CategoryType(), $category);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_category_show', array('id' => $category->getId())));
        }

        return array(
            'category'    => $category,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Deletes a Inventory\Category entity.
     *
     * @Route("/{id}/delete", name="inventory_category_delete")
     * @ParamConverter("category", class="OktolabRentBundle:Inventory\Category")
     * @Method("GET")
     */
    public function deleteAction(Category $category)
    {
        if ($category->getItems()->count() != 0) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Kann nicht gelöscht werden! Besitzt noch Gegenstände!'
            );
            return $this->redirect($this->generateUrl('inventory_category_edit', array('id' => $category->getId())));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirect($this->generateUrl('inventory_category'));
    }
}
