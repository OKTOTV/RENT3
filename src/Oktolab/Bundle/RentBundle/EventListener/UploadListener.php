<?php
namespace Oktolab\Bundle\RentBundle\EventListener;

use Oktolab\Bundle\RentBundle\Model\UploadManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Oktolab\Bundle\RentBundle\Model\UploadableInterface;

class UploadListener
{

    private $uploadManager;
    private $orphanageManager;

    public function __construct(UploadManager $uploadManager, array $orphanageManager)
    {
        $this->$uploadManager = $uploadManager;
        $this->$orphanageManager = $orphanageManager;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof UploadableInterface) {
            $manager = $this->orphanageManager->get('gallery');
            $files = $manager->uploadFiles();

            $this->uploadManager->saveAttachmentsToEntity($args->getEntity(), $files);
        }
    }
}
