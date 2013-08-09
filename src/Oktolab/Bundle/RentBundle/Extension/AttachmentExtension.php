<?php

namespace Oktolab\Bundle\RentBundle\Extension;

use Symfony\Component\HttpKernel\KernelInterface;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment;

class AttachmentExtension extends \Twig_Extension
{
    private $uploadDir;

    public function __construct($container)
    {
        $this->uploadDir = dirname(
            $container
                ->get('router')
                ->getContext()
                ->getBaseUrl()
            ).$container
                ->getParameter('oktolab.upload_dir');
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'sauce' => new \Twig_Function_Method($this, 'getUploadPath')
        );
    }

    /**
     * Converts a string to time
     *
     * @param string $string
     * @return int
     */
    public function getUploadPath ($attachment)
    {   //TODO: get UploadPath
        if (!$attachment) {
            return 'http://placekitten.com/g/200/300';
        }
        return $this->uploadDir.$attachment->getPath().'/'.$attachment->getTitle();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'my_bundle';
    }
}
