<?php

namespace Oktolab\Bundle\RentBundle\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=30)
     */
    private $roles;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set userRole
     *
     * @param integer $userRole
     * @return User
     */
    public function setRoles($userRole)
    {
        $this->roles = $userRole;

        return $this;
    }

    /**
     * Get userRole
     *
     * @return array
     */
    public function getRoles()
    {
        return array($this->roles);
    }

    //TODO: maybe without Userinterface?
    public function getPassword()
    {
        return null;
    }


    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {

    }
}
