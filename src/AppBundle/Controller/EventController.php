<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Event;
use AppBundle\Form\EventType;
/**
 * @Route("/event")
 */
class EventController extends Controller
{
    /**
     * @Route("/{page}", name="events", requirements={"page": "\d+"}, defaults={"page": 1})
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $events = $paginator->paginate($em->getRepository('AppBundle:Event')->findEventsQuery(), $page, 10);

        return ['events' => $events];
    }

    /**
     * @Route("/{event}/show")
     * @Template()
     */
    public function showEventAction(Event $event)
    {
        return ['event' => $event];
    }

    /**
     * @Route("/new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $event = new Event();
        $form = $this->createForm(new EventType(), $event);
        $form->add('submit', 'submit', ['label' => 'oktolab.event_create_button', 'attr' => ['class' => 'btn btn-primary']]);

        if ($request->getMethod() == "POST") { //sends form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($event);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'oktolab.success_create_event');

                return $this->redirect($this->generateUrl('homepage'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'oktolab.error_create_event');
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/edit/{event}")
     * @Template()
     */
    public function editAction(Request $request, Event $event)
    {
        $form = $this->createForm(new EventType(), $event);
        $form->add('submit', 'submit', ['label' => 'oktolab.event_edit_button', 'attr' => ['class' => 'btn btn-primary']]);

        if ($request->getMethod() == "POST") { //sends form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($event);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'oktolab.success_edit_event');

                return $this->redirect($this->generateUrl('homepage'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'oktolab.error_edit_event');
            }
        }

        return ['form' => $form->createView()];
    }
}
