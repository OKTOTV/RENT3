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

         //Check if usertoken exists.
         if ($this->securityContext->getToken() === null) {
//            die(var_dump($this->securityContext));
//die('kein token');
            $token = new UserToken();
            $token->setAttribute('username', $request->get('_username'));
            $token->setAttribute('password', $request->get('_password'));

            $token = $this->authProvider->authenticate($token);

            //TODO:
            //1: authenticate User (username/password)
            //2: add user to Token
            //3: add token to securityContext

            //TODO: if auth is not succesfull, return 403
   //            $response = new Response();
   //            $response->setStatusCode(403);
   //            $event->setResponse($response);

            $this->securityContext->setToken($token);
         }
//         die(var_dump($this->securityContext));
         return;

     }
}

