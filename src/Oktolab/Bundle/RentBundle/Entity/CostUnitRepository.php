<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CostUnitRepository.
 *
 * @author rt
 */
class CostUnitRepository extends EntityRepository
{
    /**
     * Counts CostUnit Entities.
     *
     * @return int
     */
    public function countAll()
    {
        return (int) $this->getEntityManager()
            ->createQuery('SELECT COUNT(c.id) FROM OktolabRentBundle:CostUnit c')
            ->getSingleScalarResult();
    }
}
