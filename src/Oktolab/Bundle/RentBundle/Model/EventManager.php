<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Monolog\Logger;

use Oktolab\Bundle\RentBundle\Model\RentableInterface;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventObject;

class EventManager
{
    /**
     * @var \Doctrine\Common\Persistence\EntityManager
     */
    protected $em = null;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger = null;

    /**
     * @var array
     */
    protected $repositories = array();

    /**
     * @var \Oktolab\Bundle\RentBundle\Entity\EventRepository
     */
    protected $eventRepository = null;

    /**
     * Constructor.
     *
     * @param ObjectManager $om
     * @param Logger        $logger
     */
    public function __construct(EntityManager $em, Logger $logger = null)
    {
        $this->em = $em;
        $this->logger = $logger;

        $this->eventRepository = $this->em->getRepository('OktolabRentBundle:Event');
    }

    /**
     * Register a Repository, so the EventManager can use it.
     *
     * @param EntityRepository $repository
     */
    public function addRepository(EntityRepository $repository)
    {
        $this->repositories[$repository->getClassName()] = $repository;
    }

    /**
     * Returns the Repository by Class Name.
     *
     * @param string $className
     *
     * @return EntityRepository|false
     */
    public function getRepository($className)
    {
        if (isset($this->repositories[$className])) {
            return $this->repositories[$className];
        }

        return false;
    }

    /**
     * Returns all registered Repositories.
     *
     * @return array
     */
    public function getRepositories()
    {
        return $this->repositories;
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
        $results = $this->eventRepository->findAllForObjectCount($object, $begin, $end);

        return 0 === $results;
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
    public function rent(array $objects, \DateTime $begin, \DateTime $end)
    {
        if (0 === count($objects)) {
            throw new \BadMethodCallException('Expected array with RentableInterface objects, empty array given');
        }

        foreach ($objects as $rentableObject) {
            if (!$rentableObject instanceof RentableInterface) {
                throw new \BadMethodCallException('Object must implement RentableInterface');
            }

            if (!$this->isAvailable($rentableObject, $begin, $end)) {
                throw new \Exception('Object is not available in given time period.');
            }
        }

        $event = new Event();
        $event->setName('asdfasdf');
        $event->setBegin($begin)->setEnd($end);
        $event->setState(Event::STATE_RENTED);

        return $this->createEventObjects($event, $objects);
    }

    protected function createEventObjects(Event $event, array $objects)
    {
        $this->em->getConnection()->beginTransaction();
        try {
            foreach ($objects as $object) {
                $eventObject = new EventObject();
                $eventObject->setEvent($event)
                    ->setType($object->getType())
                    ->setObject($object->getId());

                $event->addObject($eventObject);
                $this->em->persist($eventObject);
            }

            $this->em->persist($event);
            $this->em->flush();
            $this->em->getConnection()->commit();

        } catch (Exception $e) {
            $this->em->getConnection()->rollback();
            $this->em->close();
            throw $e;
        }

        return $event;
    }
}
