<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

class AdminController extends Controller
{
    /**
     * @Configuration\Method("GET")
     * @Configuration\Route("/admin", name="admin_index")
     * @Configuration\Template()
     */
    public function indexAction()
    {
        return array();
    }
}
