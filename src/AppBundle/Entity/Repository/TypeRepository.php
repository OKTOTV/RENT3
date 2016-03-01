<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Event;

class TypeRepository extends EntityRepository
{
    public function findTypeQuery()
    {
        return $this->getEntityManager()->createQuery('SELECT t FROM AppBundle:Type t');
    }
}
