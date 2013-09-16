<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class UserToken extends AbstractToken
{
    public function __construct(array $roles = array())
    {
        parent::__construct($roles);
        $this->setAuthenticated(count($roles) > 0);
    }

    public function getCredentials()
    {
        return '';
    }
}
