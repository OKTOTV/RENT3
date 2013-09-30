<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Oktolab\Bundle\RentBundle\Model\GuzzleHubAuth;
use Oktolab\Bundle\RentBundle\Model\GuzzleHubSearch;
use Oktolab\Bundle\RentBundle\Entity\Security\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Oktolab\Bundle\RentBundle\Entity\Security\ContactCard;

class HubUserProvider implements UserProviderInterface
{

    private $entityManager;
    private $hubApiSearchClient;
    private $hubApiAuthClient;

    public function __construct(EntityManager $entityManager, GuzzleHubSearch $hubApiSearch, GuzzleHubAuth $hubApiAuth)
    {
        $this->entityManager = $entityManager;
        $this->hubApiSearchClient = $hubApiSearch;
        $this->hubApiAuthClient = $hubApiAuth;
    }

    /**
     * Returns a User for given (unique) username.
     *
     * @param type $username
     * @return type
     */
    private function getContactCardUserByUsername($username)
    {
        $contactcard = $this->hubApiSearchClient->getContactCardForUser($username);

        if ($contactcard == null) {
            throw new UsernameNotFoundException();
        }
        $user = new User();
        $user->setRoles('ROLE_USER');
        $user->setUsername($contactcard->getGuid());
        $user->setDisplayname($contactcard->getDisplayName());

        return $user;
    }

    /**
     * Saves a User to Rent and adds him to the ROLE_USER group
     *
     * @param User $user
     */
    public function addUserToRent(User $user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Returns a Rentuser by login credentials.
     * User Is null if none found
     *
     * @param type $username
     * @return User $user
     */
    public function loadUserByUsername($username)
    {
        if ($username == '') {
                return new User();
        }

            $user = $this->entityManager->getRepository('OktolabRentBundle:Security\User')
                ->findOneBy(array('username' => $username));

        if (!$user) {

            $user = $this->getContactCardUserByUsername($username);
            if ($user->getUsername() == '') {
                throw new UsernameNotFoundException();
            } else {
                $this->addUserToRent($user);
            }
        }
        return $user;
    }

    public function authenticateUserByUsernameAndPassword($username, $password)
    {
        try {
            $contactcard = $this->hubApiAuthClient->getContactCardForUserByAuthentication($username, $password);
        } catch (\Symfony\Component\Security\Core\Exception\BadCredentialsException $e) {
            return false;
        }

        if ($contactcard['uid'][0] != $username) {
            return false;
        }
        return true;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }
        return $user;
    }

    /**
     * Whether this provider supports the given user class
     * TODO: add rent userclass
     * @param string $class
     *
     * @return Boolean
     */
    public function supportsClass($class)
    {
        return $class === 'Oktolab\Bundle\RentBundle\Entity\Security\User';
    }
}
