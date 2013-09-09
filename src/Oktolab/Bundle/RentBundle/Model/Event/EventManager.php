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

    public function getObjects(Event $event)
    {
        $objects = array();
        foreach ($event->getObjects() as $object) {
//            var_dump(\Doctrine\Common\Util\ClassUtils::getRealClass(get_class($object))); die();
//            var_dump($this->getRepositories());
            if ($repository = $this->getRepository($object->getType())) {
                $objects = $this->getRepository($object->getType())->findOneBy(array('id' => $object->getId()));
            }
//            var_dump($this->getRepositories());
//            var_dump($this->getRepository($object->getType())); die();

        }

        return $objects;
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
        $event->setState(Event::STATE_LENT);

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

        } catch (\Exception $e) {
            $this->em->getConnection()->rollback();
            $this->em->close();
            throw $e;
        }

        return $event;
    }
}
