<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Form\EventType;

/**
 * Event Controller.
 */
class EventController extends Controller
{

    /**
     * Creates a new Event.
     *
     * @Route("/event", name="event_create")
     * @Method("POST")
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $form = $this->getEventForm(array('action' => $this->generateUrl('event_create')));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = $form->getData();
            $event->setState(Event::STATE_PREPARED);
            $this->get('oktolab.event_manager')->save($event);

            return $this->redirect($this->generateUrl('rentbundle_dashboard'));
        }

        var_dump($form->getErrorsAsString());
        return new Response("invalid");
    }

    /**
     * Edit an existing Event.
     *
     * // @ Cache(expires="+5 min", public="yes")
     * @Method("GET")
     * @Route("/event/{id}/edit", name="OktolabRentBundle_Event_Edit", requirements={"id"="\d+"})
     * @ParamConverter("event", class="OktolabRentBundle:Event")
     * @Template()
     *
     * @param Request $request
     * @param Event $event
     *
     * @return array
     */
    public function editAction(Request $request, Event $event)
    {
        $form = $this->getEventForm(
            array('action' => $this->generateUrl('OktolabRentBundle_Event_Update', array('id' => $event->getId()))),
            $event
        );

        $eventManager = $this->get('oktolab.event_manager');
        $objects = $eventManager->convertEventObjectsToEntites($event->getObjects());

        return array('form' => $form->createView(), 'objects' => $objects);
    }

    /**
     * Updates an existing Event or forwards to specific Action.
     *
     * @Method("PUT")
     * @Route("/event/{id}/update", name="OktolabRentBundle_Event_Update", requirements={"id"="\d+"})
     * @ParamConverter("event", class="OktolabRentBundle:Event")
     *
     * @param Request $request
     * @param Event $event
     *
     * @return Response
     */
    public function updateAction(Request $request, Event $event)
    {
        $form = $this->getEventForm(
            array('action' => $this->generateUrl('OktolabRentBundle_Event_Update', array('id' => $event->getId()))),
            $event
        );

        $form->handleRequest($request);
        if (!$form->isValid()) { // Error while handling Form. Redirecting to EditAction.
            $this->get('session')->getFlashBag()->add('error', 'There was an error while saving the form');
            return $this->redirect($this->generateUrl('event_edit', array('id' => $event->getId())));
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
            $this->get('oktolab.event_manager')->save($form->getData());
            $this->get('session')->getFlashBag()->add('success', 'Successfully updated Event.');
        }

        // Done. Redirecting to Dashboard
        return $this->redirect($this->generateUrl('rentbundle_dashboard'));
    }

    /**
     * Rent an Event.
     *
     * @Route("/event/{id}/rent", name="event_rent")
     * @Method("POST")
     * @ParamConverter("event", class="OktolabRentBundle:Event")
     *
     * @param Request $request
     * @param Event $event
     *
     * @return Response
     */
    public function rentAction(Request $request, Event $event)
    {
        // Check for hidden-input fields for each EventObject

        // var_dump($event); die();
        return new Response();
        // this action "rents" the event. STATE_RENTED
    }

    /**
     * Creates an Event Form.
     *
     * TODO: make a Service of EventForm
     *
     * @param Event $event
     * @return \Symfony\Component\Form\Form
     */
    protected function getEventForm(array $options = array(), Event $event = null)
    {
        $event = $event ?: new Event();
        $options = array_merge(
            array(
                'action' => $this->generateUrl('event_create'),
                'method' => 'PUT',
                'em'     => $this->getDoctrine()->getManager(),
            ),
            $options
        );

        return $this->createForm(new EventType(), $event, $options);
    }
}
