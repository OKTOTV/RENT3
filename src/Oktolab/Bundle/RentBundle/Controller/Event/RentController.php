<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Form\EventType;
use Oktolab\Bundle\RentBundle\Entity\SeriesEvent;
use Oktolab\Bundle\RentBundle\Form\SeriesEventType;

/**
 * Rent Controller.
 *
 * @Configuration\Route("/rent")
 */
class RentController extends Controller
{
    /**
     * Returns a new Inventory EventForm.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/inventory", name="rentbundle_create_rent_inventory")
     * @Configuration\Template("OktolabRentBundle:Event:rentInventoryForm.html.twig")
     * @Configuration\Cache(expires="next year", public="true")
     *
     * @return array
     */
    public function rentInventoryFormAction()
    {
        $event = new Event();
        $eventType = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:EventType')->findOneBy(array('name' => 'inventory'));
        $event->setType($eventType);
        $form = $this->createForm(
            new EventType(),
            $event,
            array(
                'action' => $this->generateUrl('OktolabRentBundle_Event_Create'),
                'method' => 'POST'
            )
        );

        $form->remove('cancel');
        $form->remove('delete');
        $form->remove('rent');

        $datepicker = $this->get('oktolab.event_calendar_timeblock')->getRangeForDatePicker($form->getData()->getType());

        return array(
            'form' => $form->createView(),
            'timeblock_days'  => $datepicker[0],
            'timeblock_begin' => $datepicker[1],
            'timeblock_end'   => $datepicker[2]
        );
    }

    /**
     * Returns a new Room EventForm.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/room", name="rentbundle_create_rent_room")
     * @Configuration\Template("OktolabRentBundle:Event:rentRoomForm.html.twig")
     * @Configuration\Cache(expires="next year", public="true")
     *
     * @return array
     */
    public function rentRoomFormAction()
    {
        $event = new Event();
        $eventType = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:EventType')->findOneBy(array('name' => 'room'));
        $event->setType($eventType);

        $form = $this->createForm(
            new EventType(),
            $event,
            array(
                'action' => $this->generateUrl('OktolabRentBundle_Event_Create'),
                'method' => 'POST'
            )
        );

        $form->remove('cancel');
        $form->remove('delete');
        $form->remove('rent');
        $form->remove('update');

        $datepicker = $this->get('oktolab.event_calendar_timeblock')->getRangeForDatePicker($eventType);

        return array(
            'form' => $form->createView(),
            'timeblock_days'  => $datepicker[0],
            'timeblock_begin' => $datepicker[1],
            'timeblock_end'   => $datepicker[2]
        );
    }

    /**
     * Returns a new Inventoy SeriesEvent Form
     * @Configuration\Method("GET")
     * @Configuration\Route("/series_inventory", name="orb_create_series_inventory")
     * @Configuration\Template("OktolabRentBundle:Event\SeriesEvent:seriesRentInventoryForm.html.twig")
     */
    public function rentInventorySeriesAction()
    {
        $series_event = new SeriesEvent();

        $form = $this->createForm(
            new SeriesEventType(),
            $series_event,
            array(
                'method' => 'POST',
                'action' => $this->generateUrl('orb_create_series_event', array('type' => 'inventory'))
            ));

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Returns a new Room SeriesEvent Form
     * @Configuration\Method("GET")
     * @Configuration\Route("/series_room", name="orb_create_series_room")
     * @Configuration\Template("OktolabRentBundle:Event\SeriesEvent:seriesRentRoomForm.html.twig")
     */
    public function rentRoomSeriesAction()
    {
        $series_event = new SeriesEvent();

        $form = $this->createForm(
            new SeriesEventType(),
            $series_event,
            array(
                'method' => 'POST',
                'action' => $this->generateUrl('orb_create_series_event', array('type' => 'room'))
            ));

        return array(
            'form' => $form->createView()
        );
    }
}
