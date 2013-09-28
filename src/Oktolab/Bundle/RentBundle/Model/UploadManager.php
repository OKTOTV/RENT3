<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment;
use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManager;

/**
 * UploadManager.
 */
class UploadManager
{
    /**
     * @var string
     */
    private $uploadPath;

    /**
     * @var \Oneup\UploaderBundle\Uploader\Storage\OrphanageStorageInterface
     */
    private $orphanManager;

    /**
     * Constructor.
     *
     * @param string $uploadPath
     * @param string $webPath
     * @param \Oneup\UploaderBundle\Uploader\Storage\OrphanageStorageInterface $orphanManager
     */
    public function __construct($uploadPath, $webPath, OrphanageManager $orphanManager)
    {
        $this->uploadPath    = $webPath.$uploadPath;
        $this->orphanManager = $orphanManager;
    }

    /**
     * Uploads stuff
     *
     * @param Attachment $attachment
     */
    public function upload(Attachment $attachment)
    {
        // the file property can be empty if the field is not required
        if (null === $attachment->getFile()) {
            return;
        }

        // move takes the target directory and then the
        // target filename to move to
        $attachment->getFile()->move(
            $this->uploadPath.$attachment->getPath(),
            $attachment->getTitle()
        );

        // clean up the file property as you won't need it anymore
        $attachment->setFile(null);
    }

    /**
     * Removes Stuff.
     *
     * @param Attachment $attachment
     */
    public function removeUpload(Attachment $attachment = null)
    {
        if ($attachment) {
            $file = $this->uploadPath.$attachment->getPath().'/'.$attachment->getTitle();
            try {
                \unlink($file);
            } catch (\Exception $exc) {
                //TODO: log failure
            }
        }
    }

    /**
     * Does Stuff.
     *
     * @param UploadableInterface   $entity
     * @param array                 $files
     * @param bool                  $picture
     *
     * @return bool true if successful
     */
    public function saveAttachmentsToEntity(UploadableInterface $entity, $picture = false)
    {
        $files = $this->orphanManager->get('gallery')->uploadFiles();

        foreach ($files as $file) {
            $attachment = new Attachment();
            $attachment->setFile($file);
            $attachment->setPath($entity->getUploadFolder());
            $attachment->setTitle($file->getFileName());

            $picture ? $entity->setPicture($attachment) : $entity->addAttachment($attachment);

            $this->upload($attachment);
        }
    }
}
