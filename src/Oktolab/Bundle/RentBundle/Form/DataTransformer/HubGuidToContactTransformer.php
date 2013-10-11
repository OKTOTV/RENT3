<?php

namespace Oktolab\Bundle\RentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Oktolab\Bundle\RentBundle\Model\HubFetchService;

class HubGuidToContactTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $hubFetchService;

    /**
     * @param ObjectManager $om
     */
    public function __construct(HubFetchService $fetchService)
    {
        $this->hubFetchService = $fetchService;
    }

    /**
     * Transforms an object (contact) to a string (guid).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($contacts)
    {
        if (count($contacts) == 0) {
            return "";
        }
        $guids = array();
        foreach($contacts as $contact) {
            $guid[] = $contact->getGuid();
        }

        return $guids;
    }

    /**
     * Transforms a string (guid) to an object (contact).
     *
     * @param  string $guid
     *
     * @return contact|null
     *
     * @throws TransformationFailedException if object (contact) is not found.
     */
    public function reverseTransform($guid)
    {
        if (!$guid) {
            return null;
        }

        $contacts = $this->hubFetchService->getContactsForGuids($guid);

        if (count($contacts) == 0) {
            throw new TransformationFailedException(
                "One or more Contacts can't be found!"
            );
        }

        return $contacts;
    }
}