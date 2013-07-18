<?php

namespace Oktolab\Bundle\RentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventController extends Controller
{
    /**
     * @Route("/api/v1/events.{_format}", name="event_getEvents", defaults={"_format"="json"}, requirements={"_format"="json"})
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
                'end'   => $date->modify(sprintf('+ %d min', $faker->randomNumber(8 * 60, 48 * 60)))->format('c'),
                'item'  => $faker->randomElement(array('items', 'item-bar', 'item-foo', 'item-baz', 'itema', 'itemb')),
            );
        }

        return new JsonResponse($arr);
    }

    /**
     * @Route("/api/v1/calendarConfiguration.{_format}", name="event_calendarConfiguration", defaults={"_format"="json"}, requirements={"_format"="json"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function calendarConfigurationAction()
    {
        $arr = array();

        $date = new \DateTime('now');
        for ($i = 0; $i <= 21; $i++) {
            switch ($date->format('w')) {
                case 0: // sonntag
                    $arr['dates'][] = array('date' => $date->format('d.m'), 'timeblocks' => array());
                    break;
                case 6: // samstag
                    $arr['dates'][] = array('date' => $date->format('d.m'), 'timeblocks' => array('09-16'));
                    break;
                default:
                    $arr['dates'][] = array('date' => $date->format('d.m'), 'timeblocks' => array('09-12', '17-20'));
            }

            $date->modify('+1 day');
        }

        return new JsonResponse($arr);
    }
}