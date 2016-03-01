<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Bprs\UserBundle\Entity\User as BaseUser;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class User extends BaseUser
{
    
}
