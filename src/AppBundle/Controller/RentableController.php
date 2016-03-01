<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Rentable;
use AppBundle\Form\RentableType;

/**
 * @Route("/backend/rentable")
 */
class RentableController extends Controller
{
    /**
     * @Route("/index/{page}", name="rentables", requirements={"page": "\d+"}, defaults={"page": 1})
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $rentables = $paginator->paginate(
            $em->getRepository('AppBundle:Rentable')->findRentableQuery(),
            $page,
            10
        );

        return ['rentables' => $rentables];
    }

    /**
     * @Route("/{rentable}/show", name="show_rentable")
     * @Template()
     */
    public function showAction(Rentable $rentable)
    {
        return ['rentable' => $rentable];
    }

    /**
     * @Route("/new", name="new_rentable")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $rentable = new Rentable();
        $form = $this->createForm(new RentableType(), $rentable);
        $form->add('submit', 'submit', ['label' => 'oktolab.rentable_create_button', 'attr' => ['class' => 'btn btn-primary']]);

        if ($request->getMethod() == "POST") { //sends form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($rentable);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'oktolab.success_create_rentable');

                return $this->redirect($this->generateUrl('rentables'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'oktolab.error_create_rentable');
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/edit/{rentable}")
     * @Template()
     */
    public function editAction(Request $request, Rentable $rentable)
    {
        $form = $this->createForm(new RentableType(), $rentable);
        $form->add('submit', 'submit', ['label' => 'oktolab.rentable_edit_button', 'attr' => ['class' => 'btn btn-primary']]);

        if ($request->getMethod() == "POST") { //sends form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($rentable);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'oktolab.success_edit_rentable');

                return $this->redirect($this->generateUrl('rentables'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'oktolab.error_edit_rentable');
            }
        }

        return ['form' => $form->createView()];
    }
}
