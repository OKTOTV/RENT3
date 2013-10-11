<?php

namespace Oktolab\Bundle\RentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
/**
 * Description of WeekdaysToArrayTransformer
 *
 * @author rs
 */
class WeekdaysToArrayTransformer implements DataTransformerInterface
{
    private $weekday_array = array(
        9 => "generic.sunday",
        8 => "generic.saturday",
        7 => "generic.friday",
        6 => "generic.thursay",
        5 => "generic.wednesday",
        4 => "generic.tuesday",
        3 => "generic.monday",
    );

    /**
     * Transforms int weekdays to array
     */
    public function transform($weekdays)
    {
        $array = array();
        //weekdays are power of 2, but start with 8. 2^3 = 8, 2^9=512
        for ($i = count($this->weekday_array)+2; $i > 2; $i--) {
            if ($weekdays >= pow(2, $i)) {
                $array[$this->weekday_array[$i]] = pow(2, $i);
                $weekdays -= pow(2, $i);
            }
        }
        return $array;
    }

    /**
     *
     * Transform array into int weekdays
     * @param type $value
     */
    public function reverseTransform($array)
    {
        return array_sum($array);
    }

}
