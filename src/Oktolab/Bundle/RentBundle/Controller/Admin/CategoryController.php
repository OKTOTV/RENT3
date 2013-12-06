<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
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
        return array('entities' => $entities);
    }

    /**
     * Creates a new Inventory\Category entity.
     *
     * @Route("/", name="inventory_category_create")
     * @Method("POST")
     * @Template("OktolabRentBundle:Admin\Category:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Category();
        $form = $this->createForm(new CategoryType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $message = $this
                ->get('translator')
                ->trans('message.category.createSuccessful', array('%categoryTitle%' => $entity->getTitle()));
            $this->get('session')->getFlashBag()->add('success', $message);

            return $this->redirect($this->generateUrl('inventory_category'));
        }

        $this->get('session')->getFlashBag()->add('error', 'message.category.createFailure');

        return array('entity' => $entity, 'form' => $form->createView());
    }

    /**
     * Displays a form to create a new Inventory\Category entity.
     *
     * @Route("/new", name="inventory_category_new")
     * @Method("GET")
     * @Cache(expires="next year")
     * @Template()
     */
    public function newAction()
    {
        $form = $this->createForm(
            new CategoryType(),
            new Category(),
            array('action' => $this->generateUrl('inventory_category_create'))
        );

        return array('form' => $form->createView());
    }

    /**
     * Finds and displays a Inventory\Category entity.
     *
     * @Route("/{id}", name="inventory_category_show")
     * @ParamConverter("category", class="OktolabRentBundle:Inventory\Category")
     * @Method("GET")
     * @Template("OktolabRentBundle:Admin\Category:show.html.twig", vars={"category"})
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
     * @Template()
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
     * @Template("OktolabRentBundle:Admin\Category:edit.html.twig")
     */
    public function updateAction(Request $request, Category $category)
    {
        $editForm = $this->createForm(
            new CategoryType(),
            $category,
            array(
                'method' => 'PUT',
                'action' => $this->generateUrl('inventory_category_update', array('id' => $category->getId()))
            )
        );

        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $message = $this
                ->get('translator')
                ->trans('message.category.changeSuccess', array('%categoryTitle%' => $category->getTitle()));
            $this->get('session')->getFlashBag()->add('success', $message);

            return $this->redirect($this->generateUrl('inventory_category'));
        }

        $this->get('session')->getFlashBag()->add('error', 'message.category.changeFailure');

        return array('category' => $category, 'edit_form' => $editForm->createView());
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
        if ($category->getItems()->count() !== 0) {
            $message = $this
                ->get('translator')
                ->trans('message.category.deleteFailure', array('%categoryTitle%' => $category->getTitle()));
            $this->get('session')->getFlashBag()->add('error', $message);

            return $this->redirect($this->generateUrl('inventory_category'));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $message = $this
            ->get('translator')
            ->trans('message.category.deleteSuccess', array('%categoryTitle%' => $category->getTitle()));
        $this->get('session')->getFlashBag()->add('success', $message);

        return $this->redirect($this->generateUrl('inventory_category'));
    }
}
