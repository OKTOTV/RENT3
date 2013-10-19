<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

use Oktolab\Bundle\RentBundle\Form\CompanySettingType;
use Oktolab\Bundle\RentBundle\Entity\CompanySetting;

/**
 * Settings Controller.
 *
 * @Configuration\Route("/admin/settings")
 */
class SettingsController extends Controller
{

    /**
     * @Configuration\Method("GET")
     * @Configuration\Route("", name="admin_setting")
     * @Configuration\Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Configuration\Method("GET")
     * @Configuration\Route("/company", name="setting_company_show")
     * @Configuration\Template()
     */
    public function showCompanyAction()
    {
        $setting = new CompanySetting();
        if ($this->get('oktolab.setting')->has('company')) {
            $setting->fromArray($this->get('oktolab.setting')->get('company'));
        }

        return array('settings' => $setting);
    }

    /**
     * @Configuration\Method("GET")
     * @Configuration\Route("/company/edit", name="setting_company_edit")
     * @Configuration\Template()
     */
    public function editCompanyAction()
    {
        $setting = new CompanySetting();
        if ($this->get('oktolab.setting')->has('company')) {
            $setting->fromArray($this->get('oktolab.setting')->get('company'));
        }

        $form = $this->createForm(
            new CompanySettingType,
            $setting,
            array(
                'action' => $this->generateUrl('setting_company_update'),
                'method' => 'PUT',
            )
        );

        return array('settings' => $setting, 'edit_form' => $form->createView());
    }

    /**
     * @Configuration\Method("PUT")
     * @Configuration\Route("/company/update", name="setting_company_update")
     * @Configuration\Template("OktolabRentBundle:Admin\Settings:editCompany.html.twig")
     */
    public function updateCompanyAction(Request $request)
    {
        $setting = new CompanySetting();
        $form = $this->createForm(new CompanySettingType(), $setting);
        $form->submit($request);

        if ($form->isValid()) {
            $this->get('oktolab.setting')->set('company', $setting->toArray());

            return $this->redirect($this->generateUrl('setting_company_show'));
        }

        return array('settings' => $setting, 'edit_form' => $form->createView());
    }
}
