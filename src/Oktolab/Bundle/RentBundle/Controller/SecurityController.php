<?php

namespace Oktolab\Bundle\RentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * @Route("/secure")
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="rentbundle_secure_login")
     * @Cache(expires="+1 day", public="true")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * The security layer will intercept this request
     *
     * @codeCoverageIgnore
     *
     * @Route("/login_check", name="rentbundle_secure_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
        return $this->redirect($this->generateUrl('rentbundle_dashboard'));
    }

    /**
     * The security layer will intercept this request
     *
     * @codeCoverageIgnore
     *
     * @Route("/logout", name="rentbundle_secure_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
}
