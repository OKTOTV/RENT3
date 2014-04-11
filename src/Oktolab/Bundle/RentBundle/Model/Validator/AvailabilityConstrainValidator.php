<?php

namespace Oktolab\Bundle\RentBundle\Model\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Oktolab\Bundle\RentBundle\Model\Event\EventManager as OktolabEventManager;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\Timeblock;
use Oktolab\Bundle\RentBundle\Model\Event\EventTimeblockService;

/**
 * Description of AvailabilityValidator
 *
 * @author rs
 */
class AvailabilityConstrainValidator extends ConstraintValidator
{
    private $eventManager;
    private $ets;

    public function __construct(OktolabEventManager $em, EventTimeblockService $ets)
    {
        $this->eventManager = $em;
        $this->ets          = $ets;
    }

    /**
     * Checks if $eventitems are free for given timerange.
     *
     *
     * @param Event $event
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($event, Constraint $constraint)
    {

        $eventtimestatus = $this->ets->EventInTimeStatus($event);
        if ($eventtimestatus == EventTimeblockService::EVENT_BEGIN_OUTATIME) {
            $this->context->addViolation($constraint->beginOutatime);
        } else if ($eventtimestatus == EventTimeblockService::EVENT_END_OUTATIME) {
            $this->context->addViolation($constraint->endOutatime);
        } else if ($eventtimestatus == EventTimeblockService::EVENT_BEGIN_END_OUTATIME) {
            $this->context->addViolation($constraint->beginOutatime);
            $this->context->addViolation($constraint->endOutatime);
        }

        if ($event->getState() != Event::STATE_DEFERRED) {
            $entities = $this->eventManager->convertEventObjectsToEntites($event->getObjects());

            foreach ($entities as $entity) {
                if ($entity->getType() == 'item' && !$entity->getActive()) {
                    $this->context->addViolation($constraint->message, array('%string%' => $entity));
                } elseif (!$this->eventManager->eventObjectIsAvailable($event, $entity)) {
                    $this->context->addViolation(
                        $constraint->message,
                        array('%string%' => $entity)
                    );
                }
            }
        }
    }
}
