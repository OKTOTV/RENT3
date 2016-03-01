<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Set;
use AppBundle\Form\SetType;

/**
 * @Route("/backend/set")
 */
class SetController extends Controller
{
    /**
     * @Route("/index/{page}", name="sets", requirements={"page": "\d+"}, defaults={"page": 1})
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $sets = $paginator->paginate(
            $em->getRepository('AppBundle:Set')->findSetQuery(),
            $page,
            10
        );

        return ['sets' => $sets];
    }

    /**
     * @Route("/{set}/show", name="show_set")
     * @Template()
     */
    public function showAction(set $set)
    {
        return ['set' => $set];
    }

    /**
     * @Route("/new", name="new_set")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $set = new Set();
        $form = $this->createForm(new SetType(), $set);
        $form->add('submit', 'submit', ['label' => 'oktolab.set_create_button', 'attr' => ['class' => 'btn btn-primary']]);

        if ($request->getMethod() == "POST") { //sends form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($set);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'oktolab.success_create_set');

                return $this->redirect($this->generateUrl('sets'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'oktolab.error_create_set');
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/edit/{set}", name="edit_set")
     * @Template()
     */
    public function editAction(Request $request, Set $set)
    {
        $form = $this->createForm(new SetType(), $set);
        $form->add('submit', 'submit', ['label' => 'oktolab.set_edit_button', 'attr' => ['class' => 'btn btn-primary']]);

        if ($request->getMethod() == "POST") { //sends form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($set);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'oktolab.success_edit_set');

                return $this->redirect($this->generateUrl('sets'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'oktolab.error_edit_set');
            }
        }

        return ['form' => $form->createView()];
    }
}
