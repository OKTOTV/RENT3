<?php

namespace Oktolab\Bundle\RentBundle\Model;

/**
 * Implements Rentable Methods/Services to Objects.
 */
interface RentableInterface
{

    /**
     * Returns Object Type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns Object State
     *
     * @see Confluence Documentation
     *
     * @return int
     */
    public function getState();

    /**
     * Returns the Identifier.
     *
     * @see Confluence Documentation
     *
     * @return int
     */
    public function getId();

    /**
     * Returns Object title
     *
     * @return string
     */
    public function getTitle();
}
