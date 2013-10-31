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
     * Transforms an array object (contact) to an array of strings (guid).
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
     * Transforms a string array (guids) to an array of objects (contacts).
     *
     * @param  array $guids
     *
     * @return array contact|null
     *
     * @throws TransformationFailedException if object (contact) is not found.
     */
    public function reverseTransform($guids)
    {
        if (!$guids) {
            return null;
        }

        $contacts = array();
        foreach ($guids as $guid) {
            $contact = $this->repository->findOneBy(array('guid' => $guid));
            if (!$contact) {
                $result = $this->fetcher->getContactsForGuids(array($guid));
                $contact = $result[0];
            }
            if (!$contact) {
                throw new TransformationFailedException("One or more Contacts can't be found!");
            }
            $contacts[] = $contact;
        }

        return $contacts;
    }
}