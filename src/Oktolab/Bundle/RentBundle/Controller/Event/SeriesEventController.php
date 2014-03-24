<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

use Oktolab\Bundle\RentBundle\Entity\SeriesEvent;
use Oktolab\Bundle\RentBundle\Form\SeriesEventType;
use Oktolab\Bundle\RentBundle\Form\SeriesEventFinalizeType;

/**
 * SeriesEventController saves seriesforms with the seriesevent service and returns invalid ones
 * @Configuration\Route("/series_event")
 * @author rs
 */
class SeriesEventController extends Controller
{
    /**
     * @Configuration\Route("/create/{type}", name="orb_create_series_event")
     * @Configuration\Template()
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function createAction(Request $request, $type)
    {
        $seriesEvent = new SeriesEvent();
        $form = $this->createForm(
            new SeriesEventType(),
            $seriesEvent,
            array(
                'method' => 'POST',
                'validation_groups' => "create"
            ));
        $form->add('submit', 'submit');
        $form->handleRequest($request);
        $objects = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($seriesEvent->getObjects());
        //The Series Event per se is valid. Move on to the finalize form
        if ($form->isValid()) {
            $seriesEventService = $this->get('oktolab.series_event');
            $seriesEvent = $seriesEventService->prepareSeriesEvent($seriesEvent, $type);

            $finalize_form = $this->createForm(
                new SeriesEventFinalizeType(),
                $seriesEventService->prepareSeriesEvent($seriesEvent),
                array(
                    'method' => 'POST',
                    'action' => $this->generateUrl('orb_finalize_series_event'),
                    'validation_groups' => 'finalize'
                 ));
            $finalize_form->add('submit', 'submit');
            if ($type != "inventory") {
                return $this->render(
                    'OktolabRentBundle:Event/SeriesEvent:finalize_room.html.twig',
                    array(
                        'form'  => $finalize_form->createView(),
                        'objects' => $objects
                    )
                );
            }
            return $this->render(
                'OktolabRentBundle:Event/SeriesEvent:finalize.html.twig',
                array(
                    'form'  => $finalize_form->createView(),
                    'objects' => $objects
                )
            );
        } else { // series event is not valid.
            $this->get('session')->getFlashBag()->add('error', 'series_event.create_error');
            return array(
                'form' => $form->createView(),
                'objects' => $objects
                );
        }
    }

    /**
     * Renders a Series Finalize Form or handles the POST of it
     * @Configuration\Method({"POST"})
     * @Configuration\Template()
     * @Configuration\Route("/finalize", name="orb_finalize_series_event")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function finalizeAction(Request $request)
    {
        $series_event = new SeriesEvent();
        $form = $this->createForm(
            new SeriesEventFinalizeType(),
            $series_event,
            array(
                'method' => 'POST',
                'validation_groups' => 'finalize'
             ));
        $form->add('submit', 'submit');
        $form->handleRequest($request);
        $objects = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($series_event->getObjects());

        if ($form->isValid()) {
            $this->get('oktolab.series_event')->save($series_event);
            $this->get('session')->getFlashBag()->add('success', 'series_event.finalize_success');
            return $this->redirect($this->generateUrl('rentbundle_dashboard'));
        }
        $this->get('session')->getFlashBag()->add('error', 'series_event.finalize_error');
        return array(
            'form' => $form->createView(),
            'objects' => $objects
        );
    }

    /**
     * @Configuration\Method({"GET"})
     * @Configuration\Template()
     * @Configuration\Route("/{series_event}/show", name="orb_show_series_event")
     */
    public function showAction(SeriesEvent $series_event)
    {
        return array('series_event' => $series_event);
    }
}
