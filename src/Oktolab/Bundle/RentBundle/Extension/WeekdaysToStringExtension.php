<?php
namespace Oktolab\Bundle\RentBundle\Extension;

/**
 * Description of WeekdaysToStringExtension
 *
 * @author rs
 */
class WeekdaysToStringExtension extends \Twig_Extension
{

    private $weekday_array = array(
        9 => "generic.sunday",
        8 => "generic.saturday",
        7 => "generic.friday",
        6 => "generic.thursday",
        5 => "generic.wednesday",
        4 => "generic.tuesday",
        3 => "generic.monday",
    );
    private $weekdayShort_array = array(
        9 => "generic.sun",
        8 => "generic.sat",
        7 => "generic.fri",
        6 => "generic.thu",
        5 => "generic.wed",
        4 => "generic.tue",
        3 => "generic.mon",
    );

    private $translator;

    public function __construct($translator)
    {
        $this->translator = $translator;
    }

        /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('weekdaysToString', array($this, 'weekdayFilter'))
        );
    }

        public function weekdayFilter($weekdays, $short=false)
    {
        $array = array();
        //weekdays are power of 2, but start with 8. 2^3 = 8, 2^9=512
        for ($i = count($this->weekday_array)+2; $i > 2; $i--) {
            if ($weekdays >= pow(2, $i)) {
                if ($short) {
                    $array[] = $this->translator->trans($this->weekdayShort_array[$i]);
                    $weekdays -= pow(2, $i);
                } else {
                    $array[] = $this->translator->trans($this->weekday_array[$i]);
                    $weekdays -= pow(2, $i);
                }

            }
        }
        $array = array_reverse($array);
        return implode(', ',$array);
    }

    public function getName()
    {
        return 'timeblock';
    }
}
