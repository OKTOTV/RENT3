<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}