<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function indexAction()
    {
//        $faker = \Faker\Factory::create('de_DE');
//        $arr = array();
//        for ($i = 0; $i <= 50; $i++) {
//            $date = $faker->dateTimeBetween('-1 day', '+2 weeks');
//
//            $arr[] = array(
//                'id'    => '',
//                'title' => $faker->company(),
//                'start' => $date->format('c'),
//                'end'   => $date->modify(sprintf('+ %d min', $faker->randomNumber(10 * 60, 48 * 60)))->format('c'),
//                'item'  => $faker->randomElement(array('items', 'item-bar', 'item-foo', 'item-baz', 'itema', 'itemb')),
//            );
//        }

        $events = $this->getDoctrine()->getEntityManager()->createQueryBuilder()
                ->select('e')->from('OktolabRentBundle:Event', 'e')
                ->where('e.begin >= :now')
                ->setParameter('now', new \DateTime('today 00:00'))
                ->getQuery()->getResult();

        foreach ($events as $event) {
            $objects = $event->getObjects();
            $arr[] = array(
                'id'    => $event->getId(),
                'title' => $event->getName(),
                'start' => $event->getBegin()->format('c'),
                'end'   => $event->getEnd()->format('c'),
                'item'  => sprintf('%s%d', $objects[0]->getType(), $objects[0]->getObject()),
            );
        }

//        var_dump($arr); die();
        return new JsonResponse($arr);
    }

    /**
     * @Route("/event", name="event_create")
     * @Method("POST")
     *
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(
            new EventType(),
            new Event(),
            array(
                'action' => $this->generateUrl('event_create'),
                'method' => 'POST',
                'em'     => $this->getDoctrine()->getManager(),
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
//            var_dump($form->getNormData());
//            var_dump($form->getData()->getObjects());
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
//            return new \Symfony\Component\HttpFoundation\Response("valid");
        }

        var_dump($form->getErrorsAsString());

        return new \Symfony\Component\HttpFoundation\Response("invalid");
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
                ->getQuery()->getArrayResult();

        $serializedItems = array();
        foreach ($items as $item) {
            $serializedItems[$item['category']][sprintf('%s:%d', 'Item', $item[0]['id'])] = $item[0];
        }

//        var_dump($serializedItems);
//        return new JsonResponse($serializedItems);

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
                            array(
                                $date->modify('09:00')->format('c'), $date->modify('16:00')->format('c')
                            ),
                        ),
                    );
                    break;
                default:
                    $arr['dates'][] = array(
                        'date' => $date->format('c'),
                        'timeblocks' => array(
                            array(
                                $date->modify('09:00')->format('c'), $date->modify('12:00')->format('c')
                            ),
                            array(
                                $date->modify('17:00')->format('c'), $date->modify('20:00')->format('c')
                            ),
                        ),
                    );
            }

            $date->modify('+1 day');
        }

        $arr['items'] = $serializedItems;

        return new JsonResponse($arr);
    }
}
