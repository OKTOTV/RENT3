<?php

namespace Oktolab\Bundle\RentBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ItemRepository
 *
 * @author meh
 */
class ItemRepository extends EntityRepository
{

    /**
     * Counts all Items in database.
     *
     * @return int
     */
    public function fetchAllCount()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT COUNT(i) FROM OktolabRentBundle:Inventory\Item i')
            ->setQueryCacheLifeTime(86400)
            ->setResultCacheLifeTime(300)
            ->getSingleScalarResult();
    }

    /**
     * EAGER fetch Items with Sets and Categories - to gain performance
     *
     * @return \Doctrine\ORM\Query
     */
    public function getAllJoinSetAndJoinCategoryQuery()
    {
        $qb = $this->createQueryBuilder('i')
            ->addSelect('c')
            ->addSelect('s')
            ->leftJoin('i.category', 'c')
            ->leftJoin('i.set', 's')
            ->orderBy('i.title', 'ASC');

        return $qb->getQuery()
            ->setFetchMode('OktolabRentBundle:Inventory\Set', 'Item', 'EAGER')
            ->setFetchMode('OktolabRentBundle:Inventory\Category', 'Item', 'EAGER')
            ->setQueryCacheLifeTime(86400)   // DQL -> SQL - 1d
            ->setResultCacheLifeTime(300);   // SQL -> Result - 5m
    }
}
