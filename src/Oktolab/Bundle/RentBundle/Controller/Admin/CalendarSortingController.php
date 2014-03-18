<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use \Symfony\Component\HttpFoundation\Response;

/**
 * CalendarSortingController lists all items and categories sorted by current sorting
 * and allows resorting
 *
 * @author rs
 * @Configuration\Route("/admin/calendar/sorting")
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
     * Lists all Sets by sorting and allows resorting
     *
     * @Configuration\Route("/sets", name="orb_calendar_sorting_sets")
     * @Configuration\Method("GET")
     * @Configuration\Template()
     */
    public function setAction()
    {
        $em = $this->getDoctrine()->getManager();
        $sets = $em->getRepository('OktolabRentBundle:Inventory\Set')->findBy(array(), array('sortnumber' => 'asc'));
        return array('sets' => $sets);
    }

    /**
     * Sets sortindex of category
     *
     * @Configuration\Route("/update_category", name="orb_calendar_update_category_sorting")
     */
    public function updateCategorySorting(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = json_decode($request->getContent(), true);

        foreach ($params as $key => $value) {
            $category = $em->getRepository('OktolabRentBundle:Inventory\Category')->findOneBy(array('id' => $key));
            $category->setSortnumber($value);
            $em->persist($category);
        }
        $em->flush();
        return new Response(null, 200);
    }

    /**
     * Sets sortindex of item in category
     *
     * @Configuration\Route("/update_items", name="orb_calendar_update_item_sorting")
     */
    public function updateItemSorting(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = json_decode($request->getContent(), true);

        foreach ($params as $key => $value) {
            $item = $em->getRepository('OktolabRentBundle:Inventory\Item')->findOneBy(array('id' => $key));
            $item->setSortnumber($value);
            $em->persist($item);
        }
        $em->flush();
        return new Response(null, 200);
    }

    /**
     * Sets sortindex of sets
     *
     * @Configuration\Route("/update_sets", name="orb_calendar_update_set_sorting")
     */
    public function updateSetSorting(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = json_decode($request->getContent(), true);

        foreach ($params as $key => $value) {
            $item = $em->getRepository('OktolabRentBundle:Inventory\Set')->findOneBy(array('id' => $key));
            $item->setSortnumber($value);
            $em->persist($item);
        }
        $em->flush();
        return new Response(null, 200);
    }
}