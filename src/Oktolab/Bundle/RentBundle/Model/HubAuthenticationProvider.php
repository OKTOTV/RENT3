<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Oktolab\Bundle\RentBundle\Model\HubUserProvider;

class HubAuthenticationProvider implements AuthenticationProviderInterface {

    private $userProvider;

     public function __construct(HubUserProvider $provider)
     {
         $this->userProvider = $provider;
     }

     public function supports(TokenInterface $token)
     {
         return $token instanceof UserToken;
     }

     public function authenticate(TokenInterface $token)
     {
         $user = $this->userProvider->loadUserByUsername($token->getAttribute('username'));
         $token = new UserToken($user->getRoles());
         $token->setUser($user);
         $token->setAuthenticated(true);

         return $token;
     }
}