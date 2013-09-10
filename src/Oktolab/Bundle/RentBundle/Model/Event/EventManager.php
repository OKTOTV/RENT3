<?php

namespace Oktolab\Bundle\RentBundle\Model\Event;

//use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
//use Symfony\Bridge\Monolog\Logger;

use Oktolab\Bundle\RentBundle\Model\RentableInterface;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventObject;

use Exception\RepositoryNotFoundException;

class EventManager
{

    /**
     * @var array
     */
    protected $repositories = array();

    /**
     * Register a Repository, so the EventManager can use it.
     *
     * @param string           $name        The Class Name used by RentableInterface::getType
     * @param EntityRepository $repository  The Repository class
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function addRepository($name, EntityRepository $repository)
    {
        $this->repositories[strtolower($name)] = $repository;
    }

    /**
     * Returns the Repository by Name.
     *
     * @param string $name
     *
     * @throws RepositoryNotFoundException
     * @return EntityRepository
     */
    public function getRepository($name)
    {
        if (!isset($this->repositories[strtolower($name)])) {
            throw new Exception\RepositoryNotFoundException(sprintf('The Repository "%s" can not be found.', $name));
        }

        return $this->repositories[strtolower($name)];
    }

    /**
     * Returns the Event Repository.
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\EventRepository
     */
    public function getEventRepository()
    {
        return $this->getRepository('event');
    }

    /**
     * Checks if object is available for given time range.
     *
     * @param RentableInterface $object
     * @param DateTime          $begin
     * @param DateTime          $end
     *
     * @return boolean
     */
    public function isAvailable(RentableInterface $object, \DateTime $begin, \DateTime $end)
    {
        $results = $this->getEventRepository()->findAllForObjectCount($object, $begin, $end);
        return 0 === $results;
    }

    /**
     * Creates an Event.
     *
     * @param array $objects Add Objects to Event
     *
     * @return Event
     */
    public function create(array $objects = array())
    {
        $event = new Event();
        $event->setState(Event::STATE_PREPARED);

        $eventObjects = $this->prepareEventObjects($objects);
        foreach ($eventObjects as $object) {
            $event->addObject($object);
            $object->setEvent($event);
        }

        return $event;
    }

    public function rent(Event $event)
    {
        if (0 === count($event->getObjects())) {
            throw new Exception\MissingEventObjectsException('No EventObjects given.');
        }

        foreach ($event->getObjects() as $object) {
            if (!$this->isAvailable($object, $event->getBegin(), $event->getEnd())) {
                throw new \Exception();
            }
        }

        $event->setState(Event::STATE_LENT);
        return $event;
    }

    /**
     * Cancel an Event.
     *
     * @param Event $event
     *
     * @return boolean true if successful
     */
    public function cancel(Event $event)
    {

    }

    /**
     * Prepares EventObjects and returns them.
     *
     * @param array $objects mixed array of RentableInterface-Objects and/or EventObjects
     *
     * @throws \BadMethodCallException
     * @return array    array with EventObjects
     */
    protected function prepareEventObjects(array $objects)
    {
        $eventObjects = array();
        foreach ($objects as $object) {
            if ($object instanceof RentableInterface) {
                $eventObject = new EventObject();
                $eventObject->setObject($object->getId())
                    ->setType($object->getType());

                $eventObjects[] = $eventObject;
            } elseif ($object instanceof EventObject) {
                $eventObjects[] = $object;
            } else {
                throw new \BadMethodCallException(
                    sprintf(
                        'Neither an EventObject nor implements RentableInterface. Object of type "%s" given.',
                        \is_object($object) ? \get_class($object) : \gettype($object)
                ));
            }
        }

        return $eventObjects;
    }

    /**
     * Rents Objects and returns the rented Event
     *
     * @TODO: Kostenstellen management
     *
     * @param array     $objects Array of RentableInterface Objects.
     * @param \DateTime $begin   Begin time of the Event.
     * @param \DateTime $end     End time of the Event.
     *
     * @throws \BadMethodCallException on invalid $objects array
     * @throws \Exception              on not available object
     *
     * @return Event
     */
//    public function rent(array $objects, \DateTime $begin, \DateTime $end)
//    {
//        if (0 === count($objects)) {
//            throw new \BadMethodCallException('Expected array with RentableInterface objects, empty array given');
//        }
//
//        foreach ($objects as $rentableObject) {
//            if (!$rentableObject instanceof RentableInterface) {
//                throw new \BadMethodCallException('Object must implement RentableInterface');
//            }
//
//            if (!$this->isAvailable($rentableObject, $begin, $end)) {
//                throw new \Exception('Object is not available in given time period.');
//            }
//        }
//
//        $event = new Event();
//        $event->setName('asdfasdf');
//        $event->setBegin($begin)->setEnd($end);
//        $event->setState(Event::STATE_LENT);
//
//        return $this->createEventObjects($event, $objects);
//    }
//
//    protected function createEventObjects(Event $event, array $objects)
//    {
//        $this->em->getConnection()->beginTransaction();
//        try {
//            foreach ($objects as $object) {
//                $eventObject = new EventObject();
//                $eventObject->setEvent($event)
//                    ->setType($object->getType())
//                    ->setObject($object->getId());
//
//                $event->addObject($eventObject);
//                $this->em->persist($eventObject);
//            }
//
//            $this->em->persist($event);
//            $this->em->flush();
//            $this->em->getConnection()->commit();
//
//        } catch (\Exception $e) {
//            $this->em->getConnection()->rollback();
//            $this->em->close();
//            throw $e;
//        }
//
//        return $event;
//    }
}
