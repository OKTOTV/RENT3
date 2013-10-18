<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Form\CompanySettingType;
use Oktolab\Bundle\RentBundle\Entity\CompanySetting;

/**
 * Inventory\Place controller.
 *
 * @Route("/admin/setting")
 */
class SettingController extends Controller
{
    /**
     * @Route("/", name="admin_setting")
     * @Template("OktolabRentBundle:Admin\Setting:index.html.twig")
     */
    public function index()
    {
        return array();
    }

    /**
     * @Route("/company", name="setting_company_show")
     * @Template("OktolabRentBundle:Admin\Setting:show_company.html.twig")
     */
    public function showCompanySettings()
    {
        $companySetting = new CompanySetting();

        if ($this->get('oktolab.setting')->has('company')) {
            $companySetting->setWithArray($this->get('oktolab.setting')->get('company'));
        }

        return array('settings' => $companySetting);
    }

    /**
     * @Route("/company/edit", name="setting_company_edit")
     * @Template("OktolabRentBundle:Admin\Setting:edit_company.html.twig")
     */
    public function editCompanySettings()
    {
        $companySetting = new CompanySetting();
        if ($this->get('oktolab.setting')->has('company')) {
            $companySetting->setWithArray($this->get('oktolab.setting')->get('company'));
        }

        $form = $this->createForm(
            new CompanySettingType,
            $companySetting,
            array(
                'action' => $this->generateUrl('setting_company_update'),
                'method' => 'PUT'
            )
        );

        return array(
            'settings' => $companySetting,
            'edit_form' => $form->createView()
        );
    }

    /**
     * @Route("/company/update", name="setting_company_update")
     * @Method("PUT")
     * @Template("OktolabRentBundle:Admin\Setting:edit_company.html.twig")
     */
    public function updateCompanySettings(Request $request)
    {
        $companySetting = new CompanySetting();
        if ($this->get('oktolab.setting')->has('company')) {
            $companySetting->setWithArray($this->get('oktolab.setting')->get('company'));
        }
        $editForm = $this->createForm(new CompanySettingType(), $companySetting);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $this->get('oktolab.setting')->set('company', $companySetting->getValueArray());
            return $this->redirect($this->generateUrl('setting_company_show'));
        }

        return array(
            'settings' => $companySetting,
            'edit_form' => $editForm->createView()
        );
    }
}
