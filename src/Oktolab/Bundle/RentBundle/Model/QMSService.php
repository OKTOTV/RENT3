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
        $event->setState(Event::STATE_COMPLETED);
        foreach ($event->getQmss() as $qms) {
            $qms->getItem()->setActive(true);
            if ($qms->getStatus() > Qms::STATE_DAMAGED && $qms->getActive()) {
                $qms->getItem()->setActive(false);
                $this->em->persist($qms->getItem());
                if ($qms->getStatus() == Qms::STATE_DEFERRED) {
                    $event->setState(Event::STATE_DEFERRED);
                }
            }
            $this->em->persist($qms);
        }
        $this->em->persist($event);
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
        } else {
            $qms->getItem()->setActive(true);
        }

        foreach ($qms->getItem()->getQmss() as $old_qms) {
            if ($old_qms->getActive() && $old_qms->getStatus() > Qms::STATE_DAMAGED) {
                $qms->getItem()->setActive(false);
            }
        }
        $this->em->persist($qms->getItem());
        $this->em->flush();
    }

    /**
     * Prepares event with qms for given items
     * Used to create the EventQmsForm
     * @param \Oktolab\Bundle\RentBundle\Entity\Event $event
     * @param type $entities
     */
    public function prepareEvent(Event $event, $entities)
    {
        // Deferred event.
        // Deferred items of this event get a new QMS. The old one gets inactive.
        if ($event->getState() == Event::STATE_DEFERRED) {
            foreach ($event->getQmss() as $qms) {
                if ($qms->getStatus() == Qms::STATE_DEFERRED) {
                    $qms->setActive(false);
                    $this->addQms($event, $qms->getItem());
                }
            }
        } else {
            foreach ($entities as $item) {
                $this->addQms($event, $item);
            }
        }
    }

    /**
     * Prepares QMS and adds them to the event based on state
     * @param type $event
     * @param type $item
     */
    private function addQms($event, $item)
    {
        if ($item->getType() == 'item') {
            $qms = new Qms();
            $qms->setItem($item);
            $qms->setEvent($event);
            $event->addQms($qms);
        }
    }
}
