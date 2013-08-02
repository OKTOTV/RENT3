<?php

namespace Oktolab\Bundle\RentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Oktolab\Bundle\RentBundle\Model\RentableInterface;

/**
 * Transforms an individual rentable Object to an EventObject
 *
 * @author meh
 */
class RentableObjectToEventObjectTransformer implements DataTransformerInterface
{
    /**
     * @var EnhtityManager
     */
    private $em = null;

    /**
     * Constructor.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object that implements RentableInterface to an EventObject.
     *
     * @param  RentableInterface|null $object
     *
     * @return EventObject
     *
     * @throws TransformerFailedException if object does not implement RentableInterface
     */
    public function transform($object)
    {
        if (null === $object || 0 == count($object)) {
            return '';
        }

        var_dump($object); die();

        if (!$object instanceof RentableInterface) {
            throw new TransformationFailedException(sprintf(
               'Object must be implement RentableInterface, Object of type "%s" given.',
                \gettype($object) == 'object' ? \get_class($object) : \gettype($object)
            ));
        }

        $eventObject = new EventObject();
        $eventObject
            ->setType($object->getType())
            ->setObject($object->getId());

        return $eventObject;
    }

    /**
     * Transforms an EventObject to an individual RentableInterface Object
     *
     * @param  EventObject $eventObject
     *
     * @return RentableInterface|null
     *
     * @throws TransformationFailedException if object is not found.
     * @throws TransformationFailedException if $eventObject is not instanceof EventObject.
     */
    public function reverseTransform($eventObject)
    {
        if (!$eventObject) {
            return null;
        }

        // fail if there is an wrong object given
        if (!$eventObject instanceof EventObject) {
            throw new TransformationFailedException(sprintf(
               'Object must be instance of EventObject, Object of type "%s" given.',
                \get_type($eventObject) == 'object' ? \get_class($eventObject) : \get_type($eventObject)
            ));
        }

        // search object in repository
        $object = $this->em
            ->getRepository(sprintf(
                'OktolabRentBundle:Inventory\%s',
                $eventObject->getType()
            ))->findOneBy(array('id' => $eventObject->getObject()));

        // fail if no object were found
        if (null === $object) {
            throw new TransformationFailedException(sprintf(
                'An "%s" with id "%d" does not exist!',
                $eventObject->getType(),
                $eventObject->getObject()
            ));
        }

        return $object;
    }
}
