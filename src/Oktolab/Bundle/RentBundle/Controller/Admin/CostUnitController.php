<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;
use Oktolab\Bundle\RentBundle\Form\CostUnitType;

/**
 * CostUnit Controller.
 *
 * @Configuration\Route("/admin/costunit")
 */
class CostUnitController extends Controller
{

    /**
     * Lists all CostUnits.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/page={page}", name="admin_costunit", defaults={"page"=1}, requirements={"page"= "\d+"})
     * @Configuration\Template()
     *
     * @return array
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $resultsPerPage = 15;
        $totalResults = $em->getRepository('OktolabRentBundle:CostUnit')->countAll();

        $entities = $em->getRepository('OktolabRentBundle:CostUnit')
                ->findBy(array(), null, $resultsPerPage, $resultsPerPage * ($page - 1));

        return array(
            'entities'      => $entities,
            'currentPage'   => $page,
            'pages'         => floor($totalResults / $resultsPerPage),
            'renderPages'   => 9,
        );
    }

    /**
     * Creates a new CostUnit Entity.
     *
     * @Configuration\Route("/", name="admin_costunit_create")
     * @Configuration\Method("POST")
     * @Configuration\Template("OktolabRentBundle:Admin\CostUnit:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new CostUnit();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($form->get('contacts')->getData()) {
                foreach ($form->get('contacts')->getData() as $contact) {
                    $contact->setCostunit($entity);
                    $em->persist($contact);
                }
                $entity->setContacts($form->get('contacts')->getData());
            }

            $entity->setGuid(uniqid());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_costunit_show', array('id' => $entity->getId())));
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a CostUnit entity.
    *
    * @param CostUnit $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(CostUnit $entity)
    {
        $form = $this->createForm(new CostUnitType(), $entity, array(
            'hubTransformer' => $this->get('oktolab.hub_guid_contact_transformer'),
            'mainContactChoices' => $entity->getContacts(),
            'action' => $this->generateUrl('admin_costunit_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new CostUnit entity.
     *
     * @Configuration\Route("/new", name="admin_costunit_new")
     * @Configuration\Method("GET")
     * @Configuration\Template()
     */
    public function newAction()
    {
        $entity = new CostUnit();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a CostUnit entity.
     *
     * @Configuration\Route("/{id}", name="admin_costunit_show")
     * @Configuration\ParamConverter("costunit", class="OktolabRentBundle:CostUnit")
     * @Configuration\Method("GET")
     * @Configuration\Template()
     */
    public function showAction(CostUnit $costunit)
    {
        return array('costunit' => $costunit);
    }

    /**
     * Displays a form to edit an existing CostUnit entity.
     *
     * @Configuration\Route("/{id}/edit", name="admin_costunit_edit")
     * @Configuration\ParamConverter("costunit", class="OktolabRentBundle:CostUnit")
     * @Configuration\Method("GET")
     * @Configuration\Template()
     */
    public function editAction(CostUnit $costunit)
    {
        $editForm = $this->createEditForm($costunit);

        return array(
            'costunit'    => $costunit,
            'form'   => $editForm->createView()
        );
    }

    /**
    * Creates a form to edit a CostUnit entity.
    *
    * @param CostUnit $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CostUnit $entity)
    {
        $form = $this->createForm(new CostUnitType(), $entity, array(
            'hubTransformer' => $this->get('oktolab.hub_guid_contact_transformer'),
            'mainContactChoices' => $entity->getContacts(),
            'action' => $this->generateUrl('admin_costunit_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing CostUnit entity.
     *
     * @Configuration\Route("/{id}", name="admin_costunit_update")
     * @Configuration\Method("PUT")
     * @Configuration\ParamConverter("costunit", class="OktolabRentBundle:CostUnit")
     * @Configuration\Template("OktolabRentBundle:Admin\CostUnit:edit.html.twig")
     */
    public function updateAction(Request $request, CostUnit $costunit)
    {
        $allContacts = array();
        foreach($costunit->getContacts() as $contact) {
            $allContacts[] = $contact;
        }

        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($costunit);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            foreach ($allContacts as $contact) {
                $costunit->removeContact($contact);
                $contact->removeCostunit($costunit);
            }

            foreach ($editForm->get('contacts')->getData() as $contact) {
                $costunit->addContact($contact);
                $contact->addCostunit($costunit);
            }
            //die(var_dump(count($costunit->getContacts())));
            $em->persist($costunit);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_costunit_show', array('id' => $costunit->getId())));
        }

        return array(
            'costunit' =>   $costunit,
            'form'     =>   $editForm->createView()
        );
    }

    /**
     * Deletes a CostUnit entity.
     *
     * @Configuration\Route("/{id}/delete", name="admin_costunit_delete")
     * @Configuration\ParamConverter("costunit", class="OktolabRentBundle:CostUnit")
     * @Configuration\Method("GET")
     */
    public function deleteAction(CostUnit $costunit)
    {
        if (count($costunit->getContacts()) > 0) {
            $this
                ->get('session')
                ->getFlashBag()
                ->add(
                    'warning',
                    $this->get('translator')->trans('costunit.message.deletefailure')
                );
        } else {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->remove($costunit);
            $em->flush();

            $this
                ->get('session')
                ->getFlashBag()
                ->add(
                    'success',
                    $this->get('translator')->trans('costunit.message.deletesuccess')
                );
        }

        return $this->redirect($this->generateUrl('admin_costunit'));
    }
}
