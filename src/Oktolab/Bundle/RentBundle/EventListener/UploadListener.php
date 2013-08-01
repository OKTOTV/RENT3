<?php
namespace Oktolab\Bundle\RentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use \Oktolab\Bundle\RentBundle\Model\UploadableInterface;
use Symfony;

class UploadListener
{
    private $uploadManager = null;

    public function setUploadManager($uploadManager)
    {
        $this->uploadManager = $uploadManager;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof UploadableInterface) {
            if ($entity->getAttachment() && $entity->getAttachment()->getFile()) {
                $entity->getAttachment()->setTitle($entity->getTitle().'.'.$entity->getAttachment()->getFile()->getExtension());
                $entity->getAttachment()->setPath($entity->getUploadFolder());
                $this->uploadManager->upload($entity->getAttachment());
            } else {
                $entity->setAttachment();
            }
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
//        die('FUSRODAH');
        $entity = $args->getEntity();
        if ($entity instanceof UploadableInterface) {

            if ($entity->getAttachment() && $entity->getAttachment()->getFile()) {
                $entity->getAttachment()->setTitle($entity->getTitle().'.'.$entity->getAttachment()->getFile()->getExtension());
                $entity->getAttachment()->setPath($entity->getUploadFolder());
                $this->uploadManager->upload($entity->getAttachment());
            } else {
                $entity->setAttachment();
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        switch (\get_class($entity)) {
            case 'Oktolab\Bundle\RentBundle\Entity\Inventory\Item':
                $this->uploadManager->removeUpload($entity->getAttachment());
                break;
            default:
                break;
        }
    }
}
