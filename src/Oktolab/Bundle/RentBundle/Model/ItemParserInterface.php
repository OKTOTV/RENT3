<?php

namespace Oktolab\Bundle\RentBundle\Model;

interface ItemParserInterface
{
    public function validateFile(\SplFileInfo $file);
    public function parse(\SplFileInfo $file);
}
