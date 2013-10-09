<?php

namespace Oktolab\Bundle\RentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Cache(expires="next year", public="true")
 */
class DefaultController extends Controller
{
    /**
     * @Cache(expires="+23 hours", public="true")
     * @Method("GET")
     * @Route("/", name="rentbundle_dashboard")
     * @Template()
     */
    public function dashboardAction()
    {
        var_dump($this->get('router') instanceof \Symfony\Component\Routing\Generator\UrlGeneratorInterface); die();
        return array();
    }

    /**
     * @Cache(expires="next year", public="true")
     * @Method("GET")
     * @Route("/about", name="rentbundle_about")
     * @Template()
     */
    public function aboutAction()
    {
        $raw = explode("\n###\n", file_get_contents($this->get('kernel')->getRootDir().'/../LICENSE'));
        $licenses = array();
        foreach ($raw as $i) {
            $header = strtok($i, "\n");
            $licenses[trim($header)] = str_replace($header, '', $i);
        }

        return array('licenses' => $licenses);
    }
}
