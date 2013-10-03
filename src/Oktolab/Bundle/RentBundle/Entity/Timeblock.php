<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timeblock
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Timeblock
{
    const WEEKDAY_MO = 8;
    const WEEKDAY_TU = 16;
    const WEEKDAY_WE = 32;
    const WEEKDAY_TH = 64;
    const WEEKDAY_FR = 128;
    const WEEKDAY_SA = 256;
    const WEEKDAY_SO = 512;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="weekdays", type="integer")
     */
    private $weekdays = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="interval_begin", type="date")
     */
    private $intervalBegin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="interval_end", type="date")
     */
    private $intervalEnd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="begin", type="time")
     */
    private $begin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="time")
     */
    private $end;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set weekdays
     *
     * @param integer $weekdays
     * @return Timeblock
     */
    public function setWeekdays($weekdays)
    {
        $this->weekdays = $weekdays;

        return $this;
    }

    /**
     * Get weekdays
     *
     * @return integer
     */
    public function getWeekdays()
    {
        return $this->weekdays;
    }

    /**
     * Set intervalBegin
     *
     * @param \DateTime $intervalBegin
     * @return Timeblock
     */
    public function setIntervalBegin($intervalBegin)
    {
        $this->intervalBegin = $intervalBegin;

        return $this;
    }

    /**
     * Get intervalBegin
     *
     * @return \DateTime
     */
    public function getIntervalBegin()
    {
        return $this->intervalBegin;
    }

    /**
     * Set intervalEnd
     *
     * @param \DateTime $intervalEnd
     * @return Timeblock
     */
    public function setIntervalEnd($intervalEnd)
    {
        $this->intervalEnd = $intervalEnd;

        return $this;
    }

    /**
     * Get intervalEnd
     *
     * @return \DateTime
     */
    public function getIntervalEnd()
    {
        return $this->intervalEnd;
    }

    /**
     * Set begin
     *
     * @param \DateTime $begin
     * @return Timeblock
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * Get begin
     *
     * @return \DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return Timeblock
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Timeblock
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set Weekdays as an Array
     * Use the Constants of Timeblock, e.g.: Timeblock::WEEKDAY_FR, Timeblock::WEEKDAY_SO
     *
     * @param array $weekdays
     * @return \Oktolab\Bundle\RentBundle\Entity\Timeblock
     */
    public function setWeekdaysAsArray(array $weekdays = array())
    {
        $this->weekdays = !empty($weekdays) ? $this->computeWeekdays($weekdays) : 0;
        return $this;
    }

    /**
     * Calculates the sum of the array weekdays
     *
     * @param array $weekdays
     * @return integer
     */
    protected function computeWeekdays(array $weekdays = array())
    {
        return array_sum($weekdays);
    }

    /**
     * Returns true, if $date is in timerange and considered as active
     *
     * @param \DateTime $date
     *
     * @return boolean
     */
    public function isActiveOnDate(\DateTime $date)
    {
        return ($date >= $this->intervalBegin && $date <= $this->intervalEnd && $this->hasWeekdayAvailable($date));
    }

    /**
     * Calculates if Timeblock has Weekday available
     *
     * @param int|\DateTime $weekday
     *
     * @return boolean
     */
    public function hasWeekdayAvailable($weekday)
    {
        $timeblockWeekdays = array(0 => 512, 6 => 256, 5 => 128, 4 => 64, 3 => 32, 2 => 16, 1 => 8);
        $weekdays = $this->weekdays;

        if ($weekday instanceof \DateTime) {
            $weekday = $timeblockWeekdays[$weekday->format('w')];
        }

        foreach ($timeblockWeekdays as $day) {
            if ($day === $weekday && ($weekdays - $day) >= 0) {
                return true;
            }

            // If we can reduce $weekdays by $day, do it. Otherwise keep it.
            $weekdays = ($weekdays - $day) >= 0 ? ($weekdays - $day) : $weekdays;
        }

        return false;
    }
}
