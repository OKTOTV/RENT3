<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Event;

class RentableRepository extends EntityRepository
{
    public function findRentableQuery()
    {
        return $this->getEntityManager()->createQuery('SELECT r FROM AppBundle:Rentable r');
    }
}
