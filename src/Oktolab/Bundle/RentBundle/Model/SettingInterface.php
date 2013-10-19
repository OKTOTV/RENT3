<?php

namespace Oktolab\Bundle\RentBundle\Model;

/**
 * Description of SettingInterface
 *
 * @author rs
 */
interface SettingInterface
{
    /**
     * Sets the values from given array.
     *
     * @param array $values
     *
     * @return self
     */
    public function fromArray(array $values);

    /**
     * Hydrates data to an array.
     *
     * @return array
     */
    public function toArray();
}
