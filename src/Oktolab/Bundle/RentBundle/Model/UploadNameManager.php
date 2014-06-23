<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;
use Oneup\UploaderBundle\Uploader\File\FileInterface;

class UploadNameManager implements NamerInterface
{
    public function name(FileInterface $file)
    {
        return sprintf('%s_%s.%s', date('Y-m-d_H:i:s'), uniqid(), $file->guessExtension());
    }
}
