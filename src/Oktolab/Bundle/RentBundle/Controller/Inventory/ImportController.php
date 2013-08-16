<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Response;

use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Form\Inventory\ImportType;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Import;

/**
 * Inventory\Item controller.
 *
 * @Route("/admin/inventory/import")
 */
class ImportController extends Controller
{
    /**
     * Lists all Inventory\Item entities.
     *
     * @Route("/", name="inventory_import")
     * @Method("GET")
     * @Cache(expires="+1 day", public="true")
     * @Template
     */
    public function indexAction()
    {
        $form = $this->createFormBuilder()->add('csv', 'file')->getForm();

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/", name="inventory_import_upload")
     * @Method("POST")
     * @Template("OktolabRentBundle:Inventory\Import:index.html.twig")
     */
    public function uploadAction(Request $request)
    {
        $form = $this->createFormBuilder()->add('csv', 'file')->getForm();
        $form->handleRequest($request);
        if($form->isValid()) {
            // save file in temp dir
            $file = $form->getData('csv');
            move_uploaded_file($file['csv']->getRealPath(),$filename = tempnam(sys_get_temp_dir(), 'Import'));


            $items = array();
            $handle = fopen($filename, 'r');
            $import = new Import();

            $validator = $this->get('validator');
            $errors = array();
            //parse file with fgetcsv
            while (($data = fgetcsv($handle)) !== FALSE) {
                if ($data[0] == "Titel") {
                    continue;
                }
                $item = new Item();
                $item->setTitle($data[0]);
                $item->setDescription($data[1]);
                $item->setBarcode($data[2]);
                $item->setBuyDate(new \DateTime($data[3]));
                $item->setSerialNumber($data[4]);
                $item->setVendor($data[5]);
                $item->setModelNumber($data[6]);

                $errors[] = $validator->validate($item);

                $import->addItem($item);
                $items[] = $item;
            }
            fclose($handle);

            if (count($errors) == 0) {
                $form = $this->createForm(
                    new ImportType(),
                    $import,
                    array(
                        'action' => $this->generateUrl('inventory_import_create')
                    )
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'CSV enthält ungültige Daten!'
                );
            }
        }

        return new Response($this->renderView(
            'OktolabRentBundle:Inventory\Import:verify.html.twig',
            array(
                'items' => $items,
                'form' => $form->createView()
            )
        ));
    }

    /**
     * @Route("/create", name="inventory_import_create")
     * @Method("POST")
     * @Template("OktolabRentBundle:Inventory\Import:index.html.twig")
     */
    public function createAction(Request $request)
    {
        $import = new Import();
        $form = $this->createForm(new ImportType(), $import);
        $form->bind($request);
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            foreach ($import->getItems() as $item) {
                $em->persist($item);
            }

            $em->flush();
            return $this->redirect($this->generateUrl('inventory_item'));
        }
    }
}