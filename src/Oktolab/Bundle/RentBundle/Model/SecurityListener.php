<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Oktolab\Bundle\RentBundle\Model\UserToken;
use Oktolab\Bundle\RentBundle\Model\HubAuthenticationProvider;
use Symfony\Component\Security\Core\SecurityContext;

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

//         die(var_dump($this->securityContext->getToken()));

         if ($this->securityContext->getToken() === null) {

            $token = new UserToken();
            $token->setAttribute('username', $request->get('_username'));
            $token->setAttribute('password', $request->get('_password'));

            //die(var_dump($token));

            $token = $this->authProvider->authenticate($token);

            $this->securityContext->setToken($token);
         }
     }
}

