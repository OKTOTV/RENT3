<?php

namespace Oktolab\Bundle\RentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
        $faker = \Faker\Factory::create('de_DE');
        $arr = array();
        for ($i = 0; $i <= 50; $i++) {
            $date = $faker->dateTimeBetween('-1 day', '+2 weeks');

            $arr[] = array(
                'id'    => '',
                'title' => $faker->company(),
                'start' => $date->format('c'),
                'end'   => $date->modify(sprintf('+ %d min', $faker->randomNumber(10 * 60, 48 * 60)))->format('c'),
                'item'  => $faker->randomElement(array('items', 'item-bar', 'item-foo', 'item-baz', 'itema', 'itemb')),
            );
        }

        return new JsonResponse($arr);
    }

    /**
     * @Route("/event", name="event_create")
     * @Method("POST")
     *
     */
    public function createAction(Request $request)
    {
        return new \Symfony\Component\HttpFoundation\Response("asdf");
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

        $arr['items'] = array();

        return new JsonResponse($arr);
    }
}
