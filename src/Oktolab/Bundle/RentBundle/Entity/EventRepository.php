<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Oktolab\Bundle\RentBundle\Model\RentableInterface;

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
     * @param \DateTime $end
     * @param mixed $hydrationMode
     *
     * @return array
     */
    public function findActiveUntilEnd(\DateTime $end, $hydrationMode = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e')
            ->from('OktolabRentBundle:Event', 'e')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->andX($qb->expr()->gte('e.begin', ':begin'), $qb->expr()->lt('e.end', ':end')),
                    $qb->expr()->andX($qb->expr()->lte('e.begin', ':begin'), $qb->expr()->gt('e.end', ':begin'))
                )
            )
            ->orderBy('e.begin', 'ASC');

        $qb->setParameter(':begin', new \DateTime('now'));
        $qb->setParameter(':end', $end);

        return $qb->getQuery()->getResult($hydrationMode);
    }

    /**
     * Finds all Events in and within the given time period
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     * @param int       $hydrationMode
     *
     * @return array
     */
    public function findAllFromBeginToEnd(\DateTime $begin, \DateTime $end, $hydrationMode = null)
    {
        return $this->getAllFromBeginToEndQuery($begin, $end)
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
    public function findAllForObjectCount(EventObject $object, \DateTime $begin = null, \DateTime $end = null)
    {
        $qb = $this->getAllFromBeginToEndQuery($begin, $end);
        return (int) $qb->select('COUNT(e.id)')
            ->join('OktolabRentBundle:EventObject', 'o')
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('o.type', ':objectType'),
                    $qb->expr()->eq('o.object', ':objectId')
                )
            )
            ->setParameter('objectType', $object->getType())
            ->setParameter('objectId', $object->getObject())
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * Returns QueryBuilder for all Events in and within the given time period
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllFromBeginToEndQuery(\DateTime $begin, \DateTime $end)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e.id')->from('OktolabRentBundle:Event', 'e')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->andX($qb->expr()->lte('e.begin', ':begin'), $qb->expr()->gt('e.end', ':begin')),
                    $qb->expr()->andX($qb->expr()->gte('e.begin', ':begin'), $qb->expr()->lt('e.end', ':end')),
                    $qb->expr()->andX($qb->expr()->lt('e.begin', ':end'), $qb->expr()->gte('e.end', ':end'))
                )
            );

        $qb->setParameter('begin', $begin);
        $qb->setParameter('end', $end);

        return $qb;
    }
}
