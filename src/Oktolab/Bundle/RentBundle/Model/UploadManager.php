<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManager;

class UploadManager
{
    private $uploadPath;
    private $entityManager;
    private $orphanManager;

    public function __construct($uploadPath, $webPath, EntityManager $entityManager, $orphanManager)
    {
        $this->uploadPath = $webPath.$uploadPath;
        $this->entityManager = $entityManager;
        $this->orphanManager = $orphanManager;
    }

    /**
     *
     * @param Attachment $attachment
     * @return type
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
     *
     * @param UploadableInterface $entity
     * @param array $files
     * @param bool picture
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