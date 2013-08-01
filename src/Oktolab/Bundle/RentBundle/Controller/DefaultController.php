<?php

namespace Oktolab\Bundle\RentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Cache(expires="next year", public="true")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="rentbundle_dashboard")
     * @Cache(expires="23 hours", public="true")
     * @Template()
     */
    public function dashboardAction()
    {
        return array();
    }

    /**
     * @Route("/about", name="rentbundle_about")
     * @Template()
     */
    public function aboutAction()
    {
        return array('licenses' => file_get_contents($this->get('kernel')->getRootDir().'/../LICENSE'));
    }

}
