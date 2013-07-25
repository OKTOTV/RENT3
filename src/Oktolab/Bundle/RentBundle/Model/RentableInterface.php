<?php

namespace Oktolab\Bundle\RentBundle\Model;

interface RentableInterface
{
    public function getType();

    public function getState();

    public function getBegin();

    public function getEnd();
}
