<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

/**
 * CalendarSortingController lists all items and categories sorted by current sorting
 * and allows resorting
 *
 * @author rs
 */
class CalendarSortingController extends Controller
{
    /**
     * Lists all Inventory\Place entities.
     *
     * @Configuration\Route("/", name="orb_calendar_sorting_index")
     * @Configuration\Method("GET")
     * @Configuration\Template()
     */
    public function indexAction()
    {
        $inventoryByCalendar = $this->get('oktolab.event_calendar_inventory_aggregator')->getCategories();
        return array('categories' => $inventoryByCalendar);
    }

    /**
     *
     */
    public function updateSorting()
    {

    }
}
