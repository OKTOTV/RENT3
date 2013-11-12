<?php

namespace Oktolab\Bundle\RentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/secure")
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="rentbundle_secure_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('rentbundle_dashboard'));
        }
        $error = null;
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else if ($request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
            $request->getSession()->remove(SecurityContext::AUTHENTICATION_ERROR);
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
