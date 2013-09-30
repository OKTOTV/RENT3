<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Guzzle\Service\Client;

class GuzzleHubAuth extends Client
{
    private $authHubClient;

    public function __construct($authHubApi)
    {
        $this->authHubClient = new Client($authHubApi);
    }

    public function getContactCardForUserByAuthentication($username, $password)
    {
        try {
            $response = $this->authHubClient->get(sprintf('?action=auth&username=%s&password=%s', $username, $password))->send();
        } catch (\Guzzle\Http\Exception\BadResponseException $e) {
            throw new \Symfony\Component\Security\Core\Exception\BadCredentialsException;
        }

        $serializedString = str_replace(
            'O:11:"ContactCard"',
            sprintf(
                'O:%d:"%s\ContactCard"',
                strlen(__NAMESPACE__)+12,
                __NAMESPACE__
            ),
            $response->getBody(true)
        );

        $contactcard = unserialize($serializedString);
        return $contactcard;
    }
}
