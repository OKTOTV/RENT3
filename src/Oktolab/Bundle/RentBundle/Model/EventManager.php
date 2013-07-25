<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Monolog\Logger;

use Oktolab\Bundle\RentBundle\Model\RentableInterface;
use Oktolab\Bundle\RentBundle\Entity\Event;

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
     * Checks if objects are available for given time range.
     *
     * @param RentableInterface $object
     * @param DateTime          $begin
     * @param DateTime          $end
     *
     * @return boolean
     */
    public function isAvailable(RentableInterface $object, \DateTime $begin, \DateTime $end)
    {
        $repository = $this->em->getRepository('OktolabRentBundle:Event');
        $qb = $repository->createQueryBuilder('e')
            ->where('e.begin >= :start')
            ->setParameter('start', $begin)
//            ->andWhere('e.end >= :end')
//
//            ->setParameter('end', $end)
            ->getQuery()
        ;

        var_dump($qb->getResult());
        //$object = $repository->findOneByName($object->getTitle());


//            $query = $qb->getQuery();
//
//// Set additional Query options
//$query->setQueryHint('foo', 'bar');
//$query->useResultCache('my_cache_id');

        print_r($object);

        return mt_rand(0, 1); // it depends
    }

    /**
     * Cancel an Event.
     *
     * @param Event $event
     *
     * @return boolean  true if successful
     */
    public function cancel(Event $event)
    {

    }

    /**
     * Rents Objects and returns the Event
     *
     * @TODO: Kostenstellen management
     *
     * @param array     $objects
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @throws \BadMethodCallException on invalid $objects array
     *
     * @return Event
     */
    public function rent(array $objects, \DateTime $begin, \DateTime $end)
    {
        foreach ($objects as $rentable) {
            if (!$rentable instanceof RentableInterface) {
                throw new \BadMethodCallException('Object must implement RentableInterface');
            }
        }

        if (!$this->isAvailable($objects, $begin, $end)) {
            throw new \Exception('todo: add custom exception');
        }

        return new Event();
    }
}
