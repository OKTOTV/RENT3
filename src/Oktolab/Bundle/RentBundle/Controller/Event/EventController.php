<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

use Oktolab\Bundle\RentBundle\Entity\Event;

/**
 * Event Controller.
 */
class EventController extends Controller
{

    /**
     * Creates a new Event.
     *
     * @Configuration\Route("/event", name="OktolabRentBundle_Event_Create")
     * @Configuration\Method("POST")
     * @Configuration\Template("OktolabRentBundle:Event\Event:edit.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $form = $this->get('form.factory')->create(
            'OktolabRentBundle_Event_Form',
            new Event(),
            array(
                'em'     => $this->getDoctrine()->getManager(),
                'method' => 'POST',
                'action' => $this->generateUrl('OktolabRentBundle_Event_Create'),
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $event = $form->getData();
            $event->setState(Event::STATE_PREPARED);

            //$this->logAction('Event created', array('event' => $event->getId()));
            $this->get('oktolab.event_manager')->save($event);

            return $this->redirect($this->generateUrl('rentbundle_dashboard'));
        }

        $objects = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($form->getData()->getObjects());
        $this->get('session')->getFlashBag()->add('error', 'There was an error while saving the form.');
        //$this->logAction('Event creation failed', array('event' => $event));

        return array('form' => $form->createView(), 'objects' => $objects);
    }

    /**
     * Edit an existing Event.
     *
     * //TODO: Cache with Last-Modified && ETag
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/event/{id}/edit", name="OktolabRentBundle_Event_Edit", requirements={"id"="\d+"})
     * @Configuration\ParamConverter("event", class="OktolabRentBundle:Event")
     * @Configuration\Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Oktolab\Bundle\RentBundle\Entity\Event   $event
     *
     * @return array
     */
    public function editAction(Request $request, Event $event)
    {
        $objects = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($event->getObjects());
        $form = $this->get('form.factory')->create(
            'OktolabRentBundle_Event_Form',
            $event,
            array(
                'em'     => $this->getDoctrine()->getManager(),
                'method' => 'PUT',
                'action' => $this->generateUrl('OktolabRentBundle_Event_Update', array('id' => $event->getId())),
            )
        );

        return array('form' => $form->createView(), 'objects' => $objects, 'event' => $event);
    }

    /**
     * Updates an existing Event or forwards to specific Action.
     *
     * @Configuration\Method("PUT")
     * @Configuration\Route("/event/{id}/update", name="OktolabRentBundle_Event_Update", requirements={"id"="\d+"})
     * @Configuration\ParamConverter("event", class="OktolabRentBundle:Event")
     * @Configuration\Template("OktolabRentBundle:Event:Event\edit.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Oktolab\Bundle\RentBundle\Entity\Event   $event
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Event $event)
    {
        $form = $this->get('form.factory')->create(
            'OktolabRentBundle_Event_Form',
            $event,
            array(
                'em'     => $this->getDoctrine()->getManager(),
                'method' => 'PUT',
                'action' => $this->generateUrl('OktolabRentBundle_Event_Update', array('id' => $event->getId())),
            )
        );

        $form->handleRequest($request);
        if (!$form->isValid()) {
            // Error while handling Form. Form is not valid - show errors.
            $objects = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($event->getObjects());
            $this->get('session')->getFlashBag()->add('error', 'There was an error while saving the form.');

            return array('form' => $form->createView(), 'objects' => $objects);
        }

        if ($form->get('rent')->isClicked()) { // User clicked Rent -> Forwarding to RENT Action
            return $this->forward(
                'OktolabRentBundle:Event\Event:rent',
                array('request' => $request, 'event' => $event->getId())
            );
        }

        if ($form->get('cancel')->isClicked()) { // User clicked Abort -> Nothing to do here
            $this->get('session')->getFlashBag()->add('success', 'Successfully canceled editing Event.');
        }

        if ($form->get('update')->isClicked()) { // User clicked Update -> Save Event
            $this->get('oktolab.event_manager')->save($event);
            $this->get('session')->getFlashBag()->add('success', 'Successfully updated Event.');
            $this->get('logger')->info('Event updated.', array('id' => $form->getData()->getId()));
        }

        // Done. Redirecting to Dashboard
        return $this->redirect($this->generateUrl('rentbundle_dashboard'));
    }

    /**
     * Rent an Event.
     *
     * @Configuration\Method("POST")
     * @Configuration\Route("/event/{id}/rent", name="event_rent")
     * @Configuration\ParamConverter("event", class="OktolabRentBundle:Event")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Oktolab\Bundle\RentBundle\Entity\Event   $event
     *
     * @return Response
     */
    public function rentAction(Request $request, Event $event)
    {
        $form = $this->get('form.factory')->create(
            'OktolabRentBundle_Event_Form',
            $event,
            array(
                'em'     => $this->getDoctrine()->getManager(),
                'method' => 'PUT',
                'action' => $this->generateUrl('OktolabRentBundle_Event_Update', array('id' => $event->getId())),
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('session')->getFlashBag()->add('success', 'Event successfully rented.');

            return $this->redirect($this->generateUrl('rentbundle_dashboard'));
        }

        // Check for hidden-input fields for each EventObject

        // var_dump($event); die();
        return new Response();
        // this action "rents" the event. STATE_RENTED
    }


    /**
     * Creates the rent PDF out of an twig template.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/event/{id}/pdf", name="event_pdf")
     * @Configuration\ParamConverter("event", class="OktolabRentBundle:Event")
     *
     * @param Event $event
     *
     * @return Response
     */
    public function rentSheetPdfAction(Event $event)
    {
        return $this->get('oktolab.rent_sheet_pdf')->createRentPDF($event);
    }

    /**
     * Logs Action Message to logger service.
     *
     * @param string $message
     * @param array  $context
     */
    protected function logAction($message, array $context = array())
    {
        $context = array_merge(
            $context,
            array(
                //'user' => $this->get('security.context')->getToken()->getUser()->getUsername()
            )
        );

        $this->get('logger')->debug($message, $context);
    }
}
