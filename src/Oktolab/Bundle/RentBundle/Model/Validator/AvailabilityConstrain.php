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
    public $message = "The Object %string% is not available for the given eventtimerange.";

    public function validatedBy()
    {
        return 'availability_validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
