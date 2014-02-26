<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Qms;
use Oktolab\Bundle\RentBundle\Form\EventQMSType;

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
        $this->get('session')->getFlashBag()->add('error', 'event.save_error');

        return array(
            'form' => $form->createView(),
            'objects' => $objects,
            'timeblock_times'  => $this->get('oktolab.event_calendar_timeblock')->getBlockJsonForType($form->getData()->getType()->getName())
        );
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
                'action' => $this->generateUrl('OktolabRentBundle_Event_Update', array('id' => $event->getId()))
            )
        );

        return array(
            'form' => $form->createView(),
            'objects' => $objects,
            'event' => $event,
            'timeblock_times'  => $this->get('oktolab.event_calendar_timeblock')->getBlockJsonForType($event->getType()->getName())
            );
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

        if ($form->get('cancel')->isClicked()) { // User clicked Abort -> Nothing to do here
            $this->get('session')->getFlashBag()->add('success', 'event.cancel_success');
        }

        if (!$form->isValid()) {

            $objects = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($event->getObjects());
            $this->get('session')->getFlashBag()->add('error', 'event.save_error');

            return array(
                'form' => $form->createView(),
                'objects' => $objects,
                'timeblock_times' => $this->get('oktolab.event_calendar_timeblock')->getBlockJsonForType($event->getType()->getName())
            );
        }

        //TODO: new objects from reserved to lent won't get added.
        if ($form->get('rent')->isClicked()) { // User clicked Rent -> Forwarding to RENT Action
            $event->setState(Event::STATE_RESERVED);
            $this->get('oktolab.event_manager')->save($event);
            return $this->forward(
                'OktolabRentBundle:Event\Event:rent',
                array('request' => $request, 'event' => $event->getId())
            );
        }


        if ($form->get('update')->isClicked()) { // User clicked Update -> Save Event
            $this->get('oktolab.event_manager')->save($event);
            $this->get('session')->getFlashBag()->add('success', 'event.update_success');
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
                'validation_groups'     => array('Event', 'Logic', 'Rent'),
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {

            // @TODO: Validator needed! ----------------
            $validation = true;
            foreach ($event->getObjects() as $object) {
                if (!$object->isScanned()) {
                    $validation = false;
                }
            }

            if (!$validation) {
                $this->get('session')->getFlashBag()->add('error', 'event.save_error');
                return $this->redirect($this->generateUrl('rentbundle_dashboard'));
            }
            //@TODO: add above to class validator ------

            $this->get('session')->getFlashBag()->add('success', 'event.rent_success');
            $event->setState(Event::STATE_LENT);

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            foreach ($event->getObjects() as $object) {
                $em->persist($object);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('rentbundle_dashboard'));
        }

        return new Response();
    }

    /**
     * Deliver an Event.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/event/{id}/deliver", name="OktolabRentBundle_Event_Deliver")
     * @Configuration\ParamConverter("event", class="OktolabRentBundle:Event")
     * @Configuration\Template("OktolabRentBundle:Event:Event\edit.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Oktolab\Bundle\RentBundle\Entity\Event   $event
     *
     * @return Response
     */
    public function deliverAction(Request $request, Event $event)
    {
        // check if event has state Event::STATE_LENT
        $form = $this->get('form.factory')->create(
            'OktolabRentBundle_Event_Form',
            $event,
            array(
                'em'     => $this->getDoctrine()->getManager(),
                'method' => 'PUT',
                'action' => $this->generateUrl('OktolabRentBundle_Event_Completed', array('id' => $event->getId())),
                'validation_groups' => array('Event', 'Logic', 'Rent'),
            )
        );

        $objects = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($event->getObjects());

        return array(
            'form' => $form->createView(),
            'objects' => $objects,
            'timeblock_times' => $this->get('oktolab.event_calendar_timeblock')->getBlockJsonForType($event->getType()->getName())
        );
    }

    /**
     * Completes an Event.
     *
     * @Configuration\Method("PUT")
     * @Configuration\Route("/event/{id}/complete", name="OktolabRentBundle_Event_Completed")
     * @Configuration\ParamConverter("event", class="OktolabRentBundle:Event")
     * @Configuration\Template("OktolabRentBundle:Event:Event\edit.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Oktolab\Bundle\RentBundle\Entity\Event   $event
     *
     * @return Response
     */
    public function completedAction(Request $request, Event $event)
    {
        $form = $this->get('form.factory')->create(
            'OktolabRentBundle_Event_Form',
            $event,
            array(
                'em'     => $this->getDoctrine()->getManager(),
                'method' => 'PUT',
                'action' => $this->generateUrl('OktolabRentBundle_Event_Completed', array('id' => $event->getId())),
                'validation_groups' => array('Event', 'Logic', 'Rent'),
            )
        );

        $form->handleRequest($request);
        if (!$form->isValid()) {
            // Error while handling Form. Form is not valid - show errors.
            $objects = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($event->getObjects());
            $this->get('session')->getFlashBag()->add('error', 'event.save_error');

            return array('form' => $form->createView(), 'objects' => $objects);
        }

        if ($form->get('cancel')->isClicked()) { // User clicked Abort -> Nothing to do here
            $this->get('session')->getFlashBag()->add('success', 'event.cancel_success');
        }

        if ($form->get('rent')->isClicked()) { // User clicked Rent -> Forwarding to RENT Action

            $this->get('session')->getFlashBag()->add('success', 'event.deliver_success');
            $event->setState(Event::STATE_DELIVERED);

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            foreach ($event->getObjects() as $object) {
                $em->persist($object);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('ORB_Event_Check', array('id' => $event->getId())));
        }
        // Done. Redirecting to Dashboard
        return $this->redirect($this->generateUrl('rentbundle_dashboard'));
    }

    /**
     * Checks an Event.
     * @Configuration\Method({"GET", "POST"})
     * @Configuration\Route("/event/{id}/check", name="ORB_Event_Check")
     * @Configuration\ParamConverter("event", class="OktolabRentBundle:Event")
     * @Configuration\Template("OktolabRentBundle:Event:Event\check.html.twig")
     *
     * @return Response
     */
    public function checkAction(Request $request, Event $event)
    {
        $entities = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($event->getObjects());
        $this->get('oktolab.qms')->prepareEvent($event, $entities);

        $states = array(
            Qms::STATE_OKAY,
            QMS::STATE_FLAW,
            QMS::STATE_DAMAGED,
            QMS::STATE_DESTROYED,
            QMS::STATE_LOST,
            Qms::STATE_DEFERRED
        );

        $form = $this->createForm(
            new EventQMSType($states),
            $event,
            array(
                'action' => $this->generateUrl('ORB_Event_Check', array('id' => $event->getId())),
                'method' => 'POST'
                )
            );
        if ($request->getMethod() == 'GET') { // wants form
            return array('form' => $form->createView());
        } else { // posts form
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('oktolab.qms')->createQMSFromEvent($event);

                $this->get('session')->getFlashBag()->add('success', 'event.complete_success');
                return $this->redirect($this->generateUrl('rentbundle_dashboard'));
            }

            $this->get('session')->getFlashBag()->add('error', 'event.complete_error');
            return array('form' => $form->createView());
        }
    }

    /**
     * Creates the rent PDF out of a template.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/event/{id}/pdf", name="event_pdf")
     * @Configuration\ParamConverter("event", class="OktolabRentBundle:Event")
     *
     * @param Event $event
     *
     * @return Response
     */
    public function rentPdfAction(Event $event)
    {
        return $this->get('oktolab.rent_sheet_pdf')->generatePdf($event, $this->get('security.context')->getToken()->getUsername());
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

    /**
     * @Configuration\Method("GET")
     * @Configuration\Route("/event/{id}/show", name="orb_event_show")
     * @Configuration\ParamConverter("event", class="OktolabRentBundle:Event")
     * @Configuration\Template("OktolabRentBundle:Event:Event\show.html.twig")
     */
    public function showAction(Event $event)
    {
        $entities = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($event->getObjects());
        return array('event' => $event, 'objects' => $entities);
    }

    /**
     * Cancel an Event
     * @Configuration\Method("GET")
     * @Configuration\Route("/event/{id}/cancel", name="orb_event_cancel")
     */
    public function cancelAction(Event $event)
    {
        if ($event->getState() <= Event::STATE_RESERVED) {
            $this->get('oktolab.event_manager')->cancel($event);
            $this->get('session')->getFlashBag()->add('success', 'event.cancelation_success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'event.cancelation_error');
        }
        $this->redirect($this->generateUrl('rentbundle_dashboard'));
    }
}
