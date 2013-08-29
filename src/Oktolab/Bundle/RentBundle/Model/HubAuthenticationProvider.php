<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Oktolab\Bundle\RentBundle\Model\HubUserProvider;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

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

         if ($this->userProvider->authenticateUserByUsernameAndPassword($token->getAttribute('username'), $token->getAttribute('password'))) {
            $token = new UserToken(array(new Role($user->getRoles())));
            $token->setUser($user);
            $token->setAuthenticated(true);
            return $token;
         }

         throw new AuthenticationException('Username/Passwort ungültig');
     }
}