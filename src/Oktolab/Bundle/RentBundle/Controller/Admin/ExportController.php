<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Inventory\Item controller.
 *
 * @Configuration\Route("/admin/inventory/export")
 */
class ExportController extends Controller
{
    /**
     * Lists all Inventory\Item entities.
     *
     * @Configuration\Route("/", name="inventory_export")
     * @Configuration\Method("GET")
     * @Configuration\Cache(expires="+1 year", public="true")
     * @Configuration\Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Downloads Inventory as CSV
     * 
     * @Configuration\Route("/download", name="inventory_download")
     * @Configuration\Method("GET")
     * @Configuration\Cache(expires="+1 day", public="true")
     */
    public function downloadAction()
    {
        $container = $this->container;
        // while (false !== ($row = $items->next())) {
            // add a line in the csv file. You need to implement a toArray() method
            // to transform your object into an array
        $response = new StreamedResponse(function() use($container) {
            $handle = fopen('php://output', 'r+');
            $em = $container->get('doctrine')->getManager();
            $items = $em->getRepository('OktolabRentBundle:Inventory\Item')->createQueryBuilder('i')->getQuery()->iterate();
                fputcsv($handle, 
                    array(
                        'title', 
                        'description', 
                        'barcode', 
                        'buydate', 
                        'serialnumber', 
                        'vendor', 
                        'modelnumber', 
                        'place', 
                        'origin_value', 
                        'daily_rent', 
                        'notice', 
                        'category'
                    )
                );

            while (false !== ($row = $items->next())) {
                $date = ($row[0]->getBuyDate()) ? $row[0]->getBuyDate()->format('d.m.Y') : '';
                fputcsv($handle, 
                    array(
                        $row[0]->getTitle(), 
                        $row[0]->getDescription(), 
                        $row[0]->getBarcode(), 
                        $date, 
                        $row[0]->getSerialnumber(), 
                        $row[0]->getVendor(), 
                        $row[0]->getModelnumber(), 
                        $row[0]->getPlace()->getTitle(), 
                        $row[0]->getOriginValue(), 
                        $row[0]->getDailyRent(), 
                        $row[0]->getNotice(), 
                        ($row[0]->getCategory()) ? $row[0]->getCategory()->getTitle() : ''
                    )
                );
                $em->detach($row[0]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename="export.csv"');

        return $response;
    }
}
