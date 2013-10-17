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
     * Sets the Setting with the value array .
     */
    public function setWithArray(array $values);

    /**
     * Gets all information as Array
     */
    public function getValueArray();
}
