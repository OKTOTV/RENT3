<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Oktolab\Bundle\RentBundle\Model\UserToken;
use Oktolab\Bundle\RentBundle\Model\HubAuthenticationProvider;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;

class SecurityListener implements ListenerInterface {

     private $authProvider;
     private $securityContext;

     public function __construct(SecurityContext $securityContext, HubAuthenticationProvider $authProvider)
     {
         $this->authProvider = $authProvider;
         $this->securityContext = $securityContext;
     }

     public function handle(GetResponseEvent $event)
     {
         $request = $event->getRequest();

         if ($this->securityContext->getToken() === null) {

            try {
                $token = new UserToken();
                $token->setAttribute('username', $request->get('_username'));
                $token->setAttribute('password', $request->get('_password'));

                $token = $this->authProvider->authenticate($token);

                $this->securityContext->setToken($token);
            } catch (\Exception $e) {
                $request->attributes->set(SecurityContext::AUTHENTICATION_ERROR, 'Username/Passwort ungültig');
                $request->getSession()->set(SecurityContext::AUTHENTICATION_ERROR, array('message' =>'Username/Passwort ungültig'));
            }
         }
     }
}

