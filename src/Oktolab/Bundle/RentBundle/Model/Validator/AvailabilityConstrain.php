<?php

namespace Oktolab\Bundle\RentBundle\Model\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Description of AvailabilityValidator
 * @Annotation
 * @author rs
 */
class AvailabilityConstrain extends Constraint
{
    public $message = "event.object_unavailable_in_timerange";//"The Object %string% is not available for the given eventtimerange.";
    public $beginOutatime = "event.begin_outatime";
    public $endOutatime = "event.end_outatime";

    public function validatedBy()
    {
        return 'availability_validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
