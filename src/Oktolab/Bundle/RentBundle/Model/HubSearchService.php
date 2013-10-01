<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Guzzle\Service\Client;

class HubSearchService extends Client
{
    public function getContactCardForUser($username)
    {
        $response = $this->get('?name='.$username.'&type=user&uidonly=1')->send();
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