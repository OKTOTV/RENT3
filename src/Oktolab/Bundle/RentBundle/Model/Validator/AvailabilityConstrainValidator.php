<?php

namespace Oktolab\Bundle\RentBundle\Model\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Oktolab\Bundle\RentBundle\Model\Event\EventManager as OktolabEventManager;
/**
 * Description of AvailabilityValidator
 *
 * @author rs
 */
class AvailabilityConstrainValidator extends ConstraintValidator
{
    private $eventManager;

    public function __construct(OktolabEventManager $em)
    {
        $this->eventManager = $em;
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
        $entities = $this->eventManager->convertEventObjectsToEntites($event->getObjects());

        foreach ($entities as $entity) {
            if (!$this->eventManager->eventObjectIsAvailable($event, $entity)) {
                $this->context->addViolation(
                    $constraint->message,
                    array('%string%' => $entity)
                );
            }
        }
    }
}
