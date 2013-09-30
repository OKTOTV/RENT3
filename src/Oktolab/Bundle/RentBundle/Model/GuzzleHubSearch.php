<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Guzzle\Service\Client;

class GuzzleHubSearch
{
    private $searchHubClient;

    public function __construct($searchHubApi)
    {
        $this->searchHubClient = new Client($searchHubApi);
    }

    public function getContactCardForUser($username)
    {
        $response = $this->searchHubClient->get('?name='.$username.'&type=user&uidonly=1')->send();

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
        return $contactcard[0];
    }
}