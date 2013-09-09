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
     * @Route("/api/v1/events.{_format}",
     *      name="event_getEvents",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json|html"})
     *
     * @return JsonResponse
     */
    public function indexAction()
    {
        $events = $this->getDoctrine()->getEntityManager()->createQueryBuilder()
                ->select('e')->from('OktolabRentBundle:Event', 'e')
                ->where('e.begin >= :now')
                ->setParameter('now', new \DateTime('today 00:00'))
                ->getQuery()->getResult();

        $events = $this->getDoctrine()->getEntityManager()->getRepository('OktolabRentBundle:Event')->findActiveUntilEnd(new \DateTime('+3 weeks'));

        $arr = array();
        foreach ($events as $event) {
            $objects = $event->getObjects();
            if (count($objects) === 0) { continue; }

            $arr[] = array(
                'id'    => $event->getId(),
                'title' => $event->getName(),
                'start' => $event->getBegin()->format('c'),
                'end'   => $event->getEnd()->format('c'),
                'item'  => sprintf('%s-%d', $objects[0]->getType(), $objects[0]->getObject()),
            );
        }

        return new JsonResponse($arr);
    }

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
            $em = $this->getDoctrine()->getManager();
            $event = $form->getData();
            $event->setState(Event::STATE_RENTED);

            foreach ($event->getObjects() as $object) {
                $object->setEvent($event);
                $em->persist($object);
            }

            $em->persist($event);
            $em->flush();

            return $this->redirect($this->generateUrl('rentbundle_dashboard'));
        }

        var_dump($form->getErrorsAsString());
        return new Response("invalid");
    }

    /**
     * Edit an existing Event.
     *
     * @Route("/event/{id}/edit", name="event_edit")
     * @Method("GET")
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
            array('action' => $this->generateUrl('event_edit', array('id' => $event->getId()))),
            $event
        );

        $eventManager = $this->get('oktolab.event_manager');
        $eventManager->addRepository($this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:Inventory\Item'));
        $objects = $eventManager->getObjects($event);
        var_dump($objects); die();
        return array(
            'form'      => $form->createView(),
            'objects'   => $objects,
        );
    }

    /**
     * Updates an existing Event.
     *
     * @Route("/event/{id}/update", name="event_update")
     * @Method("POST")
     * @ParamConverter("event", class="OktolabRentBundle:Event")
     *
     * @param Request $request
     * @param Event $event
     *
     * @return Response
     */
    public function updateAction(Request $request, Event $event)
    {

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(
            new EventType(),
            new Event(),
            array(
                'action' => $this->generateUrl('event_create'),
                'method' => 'POST',
                'em'     => $em,
            )
        );
    }

    /**
     * @Route("/event/{id}/rent", name="event_rent")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Oktolab\Bundle\RentBundle\Entity\Event $event
     */
    public function rentAction(Request $request, Event $event)
    {
        // this action "rents" the event. STATE_RENTED
    }

    /**
     * @Route("/api/v1/calendarConfiguration.{_format}",
     *      name="event_calendarConfiguration",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function calendarConfigurationAction()
    {
        $items = $this->getDoctrine()->getEntityManager()->createQueryBuilder()
                ->select('i, c.title AS category')->from('OktolabRentBundle:Inventory\Item', 'i')
                ->join('i.category', 'c')
                ->getQuery()
                ->getArrayResult();

        $serializedItems = array();
        foreach ($items as $item) {
            $serializedItems[$item['category']][sprintf('%s-%d', 'Item', $item[0]['id'])] = $item[0];
        }

        $arr = array();

        $date = new \DateTime('now');
        for ($i = 0; $i <= 21; $i++) {
            switch ($date->format('w')) {
                case 0: // sonntag
                    $arr['dates'][] = array('date' => $date->format('c'), 'timeblocks' => array());
                    break;
                case 6: // samstag
                    $arr['dates'][] = array(
                        'date' => $date->format('c'),
                        'timeblocks' => array(
                            array($date->modify('09:00')->format('c'), $date->modify('16:00')->format('c')),
                        ),
                    );
                    break;
                default:
                    $arr['dates'][] = array(
                        'date' => $date->format('c'),
                        'timeblocks' => array(
                            array($date->modify('09:00')->format('c'), $date->modify('12:00')->format('c')),
                            array($date->modify('17:00')->format('c'), $date->modify('20:00')->format('c')),
                        ),
                    );
            }

            $date->modify('+1 day');
        }

        $arr['items'] = $serializedItems;

        return new JsonResponse($arr);
    }

    /**
     * Creates an Event Form.
     *
     * @param Event $event
     * @return \Symfony\Component\Form\Form
     */
    protected function getEventForm(array $options = array(), Event $event = null)
    {
        $event = $event ?: new Event();
        $options = array_merge(array(
            'action' => $this->generateUrl('event_create'),
            'method' => 'POST',
            'em'     => $this->getDoctrine()->getManager(),
        ), $options);

        return $this->createForm(
            new EventType(),
            $event,
            $options
        );
    }
}
