<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;

class UploadNameManager implements NamerInterface
{
    public function name(UploadedFile $file)
    {
        return sprintf('%s_%s.%s', uniqid(), $file->getFilename(), $file->guessExtension());
    }
}