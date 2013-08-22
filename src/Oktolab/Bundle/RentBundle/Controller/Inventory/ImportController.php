<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\File;
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
     * @Cache(expires="+1 year", public="true")
     * @Template()
     */
    public function indexAction()
    {
        $form = $this->createFormBuilder()->add('csv', 'file')->getForm();
        return array('form' => $form->createView());
    }

    /**
     * @Route("/", name="inventory_import_upload")
     * @Method("POST")
     * @Template("OktolabRentBundle:Inventory\Import:verify.html.twig")
     */
    public function uploadAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('csv', 'file', array(
                    'constraints' => array(
                        new NotNull(),
                        new File(array('mimeTypes' => array('text/plain', 'text/csv')))
                    )
                )
            )
            ->getForm();

        $form->handleRequest($request);
        $items = array();

        if($form->isValid()) {
            // save file in temp dir
            $file = $form->getData('csv');
            $items = $this->get('oktolab.item_import_manager')->parse($file['csv']);

            if (!$this->get('oktolab.item_import_manager')->validate($items) || count($items) == 0) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'CSV enthält ungültige Daten!'
                );
                return $this->redirect($this->generateUrl('inventory_import'));
            }

            $import = new Import();
            foreach ($items as $item) {
                $import->addItem($item);
            }

            $form = $this->createForm(
                new ImportType(),
                $import,
                array(
                    'action' => $this->generateUrl('inventory_import_create')
                )
            );

            return array(
                'items' => $items,
                'form' => $form->createView()
            );
        }
        return new Response(
            $this->renderView(
                'OktolabRentBundle:Inventory\Import:index.html.twig',
                array(
                    'form'  => $form->createView()
                )
            )
        );
    }

    /**
     * @Route("/create", name="inventory_import_create")
     * @Method("POST")
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