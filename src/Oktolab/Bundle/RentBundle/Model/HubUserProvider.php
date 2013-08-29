<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Guzzle\Service\Client;
use Oktolab\Bundle\RentBundle\Entity\Security\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Oktolab\Bundle\RentBundle\Entity\Security\ContactCard;

class HubUserProvider implements UserProviderInterface {

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns a User for given (unique) username.
     *
     * @param type $username
     * @return type
     */
    private function getContactCardUserByUsername($username)
    {
        $client = new Client('http://localhost/hubTIDE/web/interface.php/api/');
        $response = $client->get('contactcardsearch?name='.$username.'&type=user&uidonly=1')->send();
        $serializedString = $response->getBody(true);

        $serializedString = str_replace('O:11:"ContactCard"', sprintf('O:%d:"%s\ContactCard"', strlen(__NAMESPACE__)+12, __NAMESPACE__), $serializedString);

        $contactcard = unserialize($serializedString);
        $user = new User();
        $user->setRoles('ROLE_USER');
        $user->setUsername($contactcard[0]->getGuid());

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
                throw new UsernameNotFoundException();
            }

            $user = $this->entityManager->getRepository('OktolabRentBundle:Security\User')->findOneBy(array('username' => $username));

        if (!$user) {

            $user = $this->getContactCardUserByUsername($username);
            if ($user->getUsername() == '') {
                throw new UsernameNotFoundException();
            } else {
                $this->addUserToRent($user);
            }
        }
        //throw new \Exception(var_dump($user));
//        die(var_dump($user));
        return $user;
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
        if ($class) {
        //allow class Oktolab\Bundle\RentBundle\Entity\Security\User
            return true;
        }
        return false;
    }



}