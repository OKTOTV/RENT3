<?php

namespace Oktolab\Bundle\RentBundle\Model;
use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Event\PostPersistEvent;

class PluploadManager
{
    private $doctrine;
    private $webPath;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function onUpload(PostUploadEvent $event)
    {
    }

    public function onPersist(PostPersistEvent $event)
    {
    }
}
