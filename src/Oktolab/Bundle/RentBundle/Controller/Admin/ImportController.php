<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\Response;

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
        $form = $this->createFormBuilder()->add('csv', 'file', array('label' => 'inventory.import.data'))->getForm();
        return array('form' => $form->createView());
    }

    /**
     * @Route("/", name="inventory_import_upload")
     * @Method("POST")
     * @Template("OktolabRentBundle:Admin\Import:verify.html.twig")
     */
    public function uploadAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add(
                'csv',
                'file',
                array(
                    'constraints' => array(
                        new NotNull(
                            array('message' => $this->get('translator')->trans('message.import.noFileSelected'))
                        ),
                        new File(
                            array(
                                'mimeTypes' => array('text/plain', 'text/csv'),
                                'mimeTypesMessage' => $this->get('translator')->trans(
                                    'message.import.filetypeInvalid',
                                    array('%fileType%' => '".csv"')
                                ),
                            )
                        )
                    )
                )
            )
            ->getForm();

        $form->handleRequest($request);
        $items = array();

        if ($form->isValid()) {
            $file = $form->getData('csv');
            $importManager = $this->get('oktolab.item_import_manager');
            $importManager->setParserMode('csv');
            //check file
            if ($importManager->validateFile($file['csv'], 1)) {
                //parse file
                $items = $importManager->parse($file['csv']);
                //validate items
                if ($importManager->validateItems($items)) {
                    //everything seems fine.
                    $import = new Import();
                    foreach ($items as $item) {
                        $import->addItem($item);
                    }

                    $form = $this->createForm(
                        new ImportType(),
                        $import,
                        array('action' => $this->generateUrl('inventory_import_create'))
                    );

                    return array(
                        'items' => $items,
                        'form' => $form->createView()
                    );

                } else {
                    //items invalid
                    $this->get('session')->getFlashBag()->add('error', 'message.import.fileContainsError');
                }

            } else {
                //file is invalid
                $this->get('session')->getFlashBag()->add('error', 'message.import.fileInvalid');
            }

            return $this->redirect($this->generateUrl('inventory_import'));
        }

        return new Response(
            $this->renderView(
                'OktolabRentBundle:Admin\Import:index.html.twig',
                array('form'  => $form->createView())
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

    /**
     * @Route("/example", name="admin_import_example")
     * @Method("GET")
     */
    public function downloadExampleAction()
    {
        $response = new Response();
        $response->setContent(file_get_contents(__DIR__.'/../../DataFixtures/Files/items.csv'));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="example.csv"');

        return $response;
    }
}
