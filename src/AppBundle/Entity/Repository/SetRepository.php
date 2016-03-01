<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Event;

class SetRepository extends EntityRepository
{
    public function findSetQuery()
    {
        return $this->getEntityManager()->createQuery('SELECT s FROM AppBundle:Set s');
    }
}
