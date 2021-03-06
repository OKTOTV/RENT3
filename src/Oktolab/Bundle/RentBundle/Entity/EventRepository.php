<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Oktolab\Bundle\RentBundle\Model\RentableInterface;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Doctrine\ORM\Query\Expr;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository
{

    /**
     * Finds all active Events until $end.
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     * @param int       $hydrationMode
     *
     * @return array
     */
    public function findActiveFromBeginToEnd(\DateTime $begin, \DateTime $end, $type = 'inventory', $hydrationMode = null)
    {
        $qb = $this->getAllFromBeginToEndQuery($begin, $end, $type);
        $qb->andWhere($qb->expr()->not($qb->expr()->eq('e.state', Event::STATE_CANCELED)))->add('orderBy', 'e.state ASC');

        return $qb->getQuery()->getResult($hydrationMode);
    }

    /**
     * Finds all Events in and within the given time period
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     * @param string  $type
     * @param int       $hydrationMode
     *
     * @return array
     */
    public function findAllFromBeginToEnd(\DateTime $begin, \DateTime $end, $type = 'inventory', $hydrationMode = null)
    {
        return $this->getAllFromBeginToEndQuery($begin, $end, $type)
            ->getQuery()->getResult($hydrationMode);
    }

    /**
     * Finds all Events for Object, optionally for given time period.
     *
     * @param RentableInterface $object
     * @param \DateTime         $begin
     * @param \DateTime         $end
     *
     * @return integer
     */
    public function findAllForObjectCount(EventObject $object, \DateTime $begin = null, \DateTime $end = null, $type = 'inventory')
    {
        $qb = $this->getAllFromBeginToEndQuery($begin, $end, $type);
        $query = $qb->select('COUNT(e.id)')
            ->leftJoin('OktolabRentBundle:EventObject', 'o', Expr\Join::WITH, 'e.id = o.event')
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('o.type', ':objectType'),
                    $qb->expr()->eq('o.object', ':objectId'),
                    $qb->expr()->notIn('e.state', array(Event::STATE_CANCELED, Event::STATE_COMPLETED))
                )
            )
            ->setParameter('objectType', $object->getType())
            ->setParameter('objectId', $object->getObject())
            ->getQuery();
        return (int) $query->getSingleScalarResult();
    }

    /**
     * Returns all active Events for given object in given timerange
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\EventObject $object
     * @param \DateTime $begin
     * @param \DateTime $end
     * @return events
     */
    public function findAllActiveForObject(RentableInterface $object, \DateTime $begin = null, \DateTime $end = null, $type = 'inventory')
    {
        $qb = $this->getAllFromBeginToEndQuery($begin, $end, $type);
        $query = $qb->select('e')
            ->leftJoin('OktolabRentBundle:EventObject', 'o', Expr\Join::WITH, 'e.id = o.event')
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('o.type', ':objectType'),
                    $qb->expr()->eq('o.object', ':objectId'),
                    $qb->expr()->notIn('e.state', array(Event::STATE_CANCELED, Event::STATE_COMPLETED))
                )
            )
            ->setParameter('objectType', $object->getType())
            ->setParameter('objectId', $object->getId())
            ->getQuery();
        return $query->getResult();
    }

    /**
     * Returns QueryBuilder for all Events in and within the given time period
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     * @param string    $type
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllFromBeginToEndQuery(\DateTime $begin, \DateTime $end, $type = 'inventory')
    {
        $qb2 = $this->getEntityManager()->createQueryBuilder();
        $qb2->select('et.id')->from('OktolabRentBundle:EventType', 'et')
            ->where($qb2->expr()->eq('et.name', ':type'))
            ;

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e')
            ->from('OktolabRentBundle:Event', 'e')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->andX($qb->expr()->lte('e.begin', ':begin'), $qb->expr()->gt('e.end', ':begin')),
                        $qb->expr()->andX($qb->expr()->gte('e.begin', ':begin'), $qb->expr()->lt('e.end', ':end')),
                        $qb->expr()->andX($qb->expr()->lt('e.begin', ':end'), $qb->expr()->gte('e.end', ':end'))
                    ),
                    $qb->expr()->in('e.type', $qb2->getDQL())
                )
            );

        $qb->setParameter('begin', $begin);
        $qb->setParameter('end', $end);
        $qb->setParameter('type', $type);

        return $qb;
    }

    public function getOverduedEvents($type='inventory')
    {
        $qb2 = $this->getEntityManager()->createQueryBuilder();
        $qb2->select('et.id')->from('OktolabRentBundle:EventType', 'et')
            ->where($qb2->expr()->eq('et.name', ':type'))
            ;

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e')
            ->from('OktolabRentBundle:Event', 'e')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->lte('e.end', ':end'),
                    $qb->expr()->in('e.type', $qb2->getDQL()),
                    $qb->expr()->orX(
                        $qb->expr()->eq('e.state', ':reserved'),
                        $qb->expr()->eq('e.state', ':lent'),
                        $qb->expr()->eq('e.state', ':delivered'),
                        $qb->expr()->eq('e.state', ':deferred')
                    )
                )
            );
        $qb->setParameter('end', new \DateTime());
        $qb->setParameter('reserved', Event::STATE_RESERVED);
        $qb->setParameter('lent', Event::STATE_LENT);
        $qb->setParameter('delivered', Event::STATE_DELIVERED);
        $qb->setParameter('deferred', Event::STATE_DEFERRED);
        $qb->setParameter('type', $type);

        return $qb->getQuery()->getResult();
    }
}
