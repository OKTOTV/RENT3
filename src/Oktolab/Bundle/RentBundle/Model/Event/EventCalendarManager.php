<?php

namespace Oktolab\Bundle\RentBundle\Model\Event;

/**
 * Manages the EventCalendar
 */
class EventCalendarManager
{

    /**
     * @var array
     */
    protected $repositories = array();

    /**
     * Adds a Repository.
     *
     * @param string                         $name
     * @param \Doctrine\ORM\EntityRepository $repository
     */
    public function addRepository($name, \Doctrine\ORM\EntityRepository $repository)
    {
        $this->repositories[strtolower($name)] = $repository;
    }

    /**
     * Returns Repository by name.
     *
     * @param string $name
     * @return \Doctrine\ORM\EntityRepository|null
     */
    public function getRepository($name)
    {
        return isset($this->repositories[strtolower($name)]) ? $this->repositories[strtolower($name)] : null;
    }

    /**
     * Returns the Timeblocks from $begin to $end.
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     * @param array     $categories
     *
     * @return array
     */
    public function getTimeblocks(\DateTime $begin, \DateTime $end, array $categories = array())
    {
        return array();
    }

    /**
     * Returns the Events from $begin to $end.
     *
     * @param \DateTime $begin  Begin DateTime
     * @param \DateTime $end    End DateTime
     * @param bool      $active Only active Events?
     *
     * @return array
     */
    public function getEvents(\DateTime $begin, \DateTime $end, array $objectTypes = array(), $active = true)
    {
        return array();
    }

    /**
     * Returns the Objects used for rendering the Inventory.
     * This function sorts Sets before Items
     *
     * @param array $objectTypes
     *
     * @return array
     */
    public function getInventoryObjects(array $objectTypes = array())
    {
        return array();
    }
}
