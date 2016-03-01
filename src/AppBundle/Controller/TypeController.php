<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Type;
use AppBundle\Form\TypeType;

/**
 * @Route("/backend/type")
 */
class TypeController extends Controller
{
    /**
     * @Route("/index/{page}", name="types", requirements={"page": "\d+"}, defaults={"page": 1})
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $types = $paginator->paginate(
            $em->getRepository('AppBundle:Type')->findTypeQuery(),
            $page,
            10
        );

        return ['types' => $types];
    }

    /**
     * @Route("/{type}/show", name="show_type")
     * @Template()
     */
    public function showAction(Type $type)
    {
        return ['type' => $type];
    }

    /**
     * @Route("/new", name="new_type")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $type = new Type();
        $form = $this->createForm(new TypeType(), $type);
        $form->add('submit', 'submit', ['label' => 'oktolab.type_create_button', 'attr' => ['class' => 'btn btn-primary']]);

        if ($request->getMethod() == "POST") { //sends form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($type);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'oktolab.success_create_type');

                return $this->redirect($this->generateUrl('types'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'oktolab.error_create_type');
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/edit/{type}", name="edit_type")
     * @Template()
     */
    public function editAction(Request $request, Type $type)
    {
        $form = $this->createForm(new TypeType(), $type);
        $form->add('submit', 'submit', ['label' => 'oktolab.type_edit_button', 'attr' => ['class' => 'btn btn-primary']]);

        if ($request->getMethod() == "POST") { //sends form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($type);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'oktolab.success_edit_type');

                return $this->redirect($this->generateUrl('types'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'oktolab.error_edit_type');
            }
        }

        return ['form' => $form->createView()];
    }
}
