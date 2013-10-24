<?php

namespace Oktolab\Bundle\RentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Oktolab\Bundle\RentBundle\Model\HubFetchService;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Hub GUID to Contact Transformer
 */
class HubGuidToContactTransformer implements DataTransformerInterface
{
    /**
     * @var \Oktolab\Bundle\RentBundle\Model\HubFetchService
     */
    protected $fetcher = null;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository = null;

    /**
     * Constructor.
     *
     * @param \Oktolab\Bundle\RentBundle\Model\HubFetchService $fetcher
     * @param \Doctrine\ORM\EntityRepository $repository
     */
    public function __construct(HubFetchService $fetcher, ObjectRepository $repository)
    {
        $this->fetcher = $fetcher;
        $this->repository = $repository;
    }

    /**
     * Transforms an object (contact) to a string (guid).
     *
     * @param  Contact|null $issue
     *
     * @return array
     */
    public function transform($contacts)
    {
        if (0 === count($contacts)) {
            return '';
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

        $contacts = $this->repository->findBy(array('guid' => $guid));
        if (0 !== $contacts) {
            return $contacts;
        }

        $contacts = $this->fetcher->getContactsForGuids($guid);
        if (0 === count($contacts)) {
            throw new TransformationFailedException(
                "One or more Contacts can't be found!"
            );
        }

        return $contacts;
    }
}