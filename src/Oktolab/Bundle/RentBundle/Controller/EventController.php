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
        $date = new \DateTime('now');
        $date2 = new \DateTime('now');
        $date3 = new \DateTime('now');

        return new JsonResponse(array(
            '123' => array(
                'id'        => 123,
                'title'     => 'EventTest',
                'item'      => 'items',
                'start'     => $date->modify('tomorrow 17:00')->format('c'),
                'end'       => $date2->modify('tomorrow +2 days 18:00')->format('c'),
            ),
            '234' => array(
                'id'        => 234,
                'title'     => 'Cool stuff',
                'item'      => 'item-bar',
                'start'     => $date->modify('tomorrow 11:00')->format('c'),
                'end'       => $date2->modify('tomorrow +2 days 18:00')->format('c'),
            ),
            '345' => array(
                'id'        => 345,
                'title'     => 'Awesome',
                'item'      => 'itema',
                'start'     => $date->modify('friday +1 week 11:00')->format('c'),
                'end'       => $date->modify('+3 days 18:00')->format('c'),
            ),
            '456' => array(
                'id'        => 456,
                'title'     => 'Robins Event',
                'item'      => 'item-foo',
                'start'     => $date3->modify('today 17:00')->format('c'),
                'end'       => $date3->modify('+2 days 11:00')->format('c'),
            ),
            '567' => array(
                'id'        => 567,
                'title'     => 'Michis Event',
                'item'      => 'item-foo',
                'start'     => $date3->modify('+1 day')->format('c'),
                'end'       => $date3->modify('+3 day')->format('c'),
            ),
        ));
    }
}