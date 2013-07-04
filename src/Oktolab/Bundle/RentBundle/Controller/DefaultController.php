<?php

namespace Oktolab\Bundle\RentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/about")
     * @Template()
     */
    public function indexAction()
    {
        return array('licenses' => file_get_contents($this->get('kernel')->getRootDir().'/../LICENSE'));
    }
}
