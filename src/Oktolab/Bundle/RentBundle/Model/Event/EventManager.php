<?php

namespace Oktolab\Bundle\RentBundle\Model\Event;

use Doctrine\ORM\EntityRepository;

use Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException;
use Oktolab\Bundle\RentBundle\Model\Event\Exception\MissingEventObjectsException;
use Oktolab\Bundle\RentBundle\Model\Event\Exception\ObjectNotAvailableException;
use Oktolab\Bundle\RentBundle\Model\RentableInterface;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventObject;

class EventManager
{

    /**
     * @var array
     */
    protected $repositories = array();

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;

    /**
     * Sets the Entity Manager
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

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
            throw new RepositoryNotFoundException(sprintf('The Repository "%s" can not be found.', $name));
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
     * @param mixed     $object
     * @param DateTime  $begin
     * @param DateTime  $end
     *
     * @return boolean
     */
    public function isAvailable($object, \DateTime $begin, \DateTime $end)
    {
        $eventObjects = $this->prepareEventObjects(array($object));
        $results = $this->getEventRepository()->findAllForObjectCount($eventObjects[0], $begin, $end);
        return 0 === $results;
    }

    public function eventObjectIsAvailable(Event $event, $object)
    {
        if (!is_array($object)) {
            $events = $this->getEventRepository()->findAllActiveForObject($object, $event->getBegin(), $event->getEnd(), $event->getType()->getName());
            if (count($events) == 0 || (count($events) == 1 && $event->getId() == $events[0]->getId())) { //TODO: doesn't always work :(
                return true;
            }
            return false;
        } else {
            foreach ($object as $setItem) {
                $events = $this->getEventRepository()->findAllActiveForObject($setItem, $event->getBegin(), $event->getEnd());
                if (count($events) > 1 || $events[0]->getId() != $event->getId()) {
                    return false;
                }
            }
            return true;
        }
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
            throw new MissingEventObjectsException('No EventObjects given.');
        }

        if (null === $event->getBegin() || null === $event->getEnd() || $event->getBegin() > $event->getEnd()) {
            throw new \LogicException('End of Event must be greather then Begin.');
        }

        foreach ($event->getObjects() as $object) {
            if (!$this->isAvailable($object, $event->getBegin(), $event->getEnd())) {
                throw new ObjectNotAvailableException('The Object is not available');
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
     * Saves the Event.
     *
     * @param Event $event
     *
     * @throws \Exception if Event could not be saved.
     *
     * @return Event
     */
    public function save(Event $event)
    {
        $event->getBarcode() ? : $event->setBarcode($this->getUniqueBarcode());
        $oldEventObjects = $this->getRepository('EventObject')->findBy(array('event' => $event->getId()));

        $this->em->getConnection()->beginTransaction();
        try {

            foreach ($oldEventObjects as $object) {
                $object->setEvent();
                $this->em->persist($object);
            }

            foreach ($event->getObjects() as $object) {
                $object->setEvent($event);
                $this->em->persist($object);
            }

            $this->em->persist($event);
            $this->em->flush();
            $this->em->getConnection()->commit();

        } catch (\Exception $e) {
            $this->em->getConnection()->rollback();
            $this->em->close();
            throw $e;
        }

        return $event;
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
                    )
                );
            }
        }

        return $eventObjects;
    }

    public function convertEventObjectsToEntites($eventObjects)
    {
        $entities = array();
        foreach ($eventObjects as $object) {
            $entities[] = $this->getRepository($object->getType())->findOneBy(array('id' => $object->getObject()));
        }

        return $entities;
    }

    private function getUniqueBarcode() {
        $barcode = substr(md5(rand(0, 1000000)), 0, 6);
        return $barcode;
    }
}
