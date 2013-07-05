<?php

namespace Oktolab\Bundle\RentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/about", name="rentbundle_about")
     * @Template()
     */
    public function aboutAction()
    {
        return array('licenses' => file_get_contents($this->get('kernel')->getRootDir().'/../LICENSE'));
    }

    /**
     * @Route("/", name="rentbundle_dashboard")
     * @Template()
     */
    public function dashboardAction()
    {
        return array();
    }
}
