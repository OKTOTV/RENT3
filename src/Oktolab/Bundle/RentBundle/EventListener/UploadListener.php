<?php
namespace Oktolab\Bundle\RentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManager;

use Oktolab\Bundle\RentBundle\Model\UploadManager;
use Oktolab\Bundle\RentBundle\Model\UploadableInterface;

/**
 * Upload Listener.
 */
class UploadListener
{

    /**
     * @var \Oktolab\Bundle\RentBundle\Model\UploadManager
     */
    private $uploadManager = null;

    /**
     * @var \Oneup\UploaderBundle\Uploader\Storage\OrphanageStorageInterface
     */
    private $orphanageManager = array();

    /**
     * Constructor.
     *
     * @param \Oktolab\Bundle\RentBundle\Model\UploadManager $uploadManager
     * @param array $orphanageManager
     */
    public function __construct(UploadManager $uploadManager, OrphanageManager $orphanageManager)
    {
        $this->uploadManager    = $uploadManager;
        $this->orphanageManager = $orphanageManager;
    }

    /**
     * Called on prePersist Events.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->saveAttachments($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->saveAttachments($args);
    }

    protected function saveAttachments(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof UploadableInterface) {
            $manager = $this->orphanageManager->get('gallery');
            $this->uploadManager->saveAttachmentsToEntity($args->getEntity(), $manager->uploadFiles());
        }
    }
}
