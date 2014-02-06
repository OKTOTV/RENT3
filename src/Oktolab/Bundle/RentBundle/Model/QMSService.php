<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Qms;
use Doctrine\ORM\EntityManager;

/**
 * QMSService creates new qms entries, sets item states depending on their specific qmss.
 *
 * @author rs
 */
class QMSService
{
    protected $em = null;

    public function __construct(EntityManager $manager)
    {
        $this->em = $manager;
    }

    /**
     * creates qms in database and sets item active states depending on qmss.
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Qms $qms
     */
    public function createQMSFromEvent(Event $event)
    {
        foreach ($event->getQmss() as $qms) {
            if ($qms->getStatus() > Qms::STATE_DAMAGED) {
                $qms->getItem()->setActive(false);
                $this->em->persist($qms->getItem());
            }
            $this->em->persist($qms);
        }
        $this->em->flush();
    }

    /**
     * creates qms in database and sets item active state depending on qms.
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Qms $qms
     */
    public function createQMS(Qms $qms)
    {
        $this->em->persist($qms);
        if ($qms->getStatus() > Qms::STATE_DAMAGED) {
            $qms->getItem()->setActive(false);
            $this->em->persist($qms->getItem());
        }

        $this->em->flush();
    }

    /**
     * Prepares event with qms for given items
     * @param \Oktolab\Bundle\RentBundle\Entity\Event $event
     * @param type $entities
     */
    public function prepareEvent(Event $event, $entities)
    {
        foreach ($entities as $item) {
            if ($item->getType() == 'item') {
                $qms = new Qms();
                $qms->setItem($item);
                $qms->setEvent($event);
                $event->addQms($qms);
            }
        }
    }
}
