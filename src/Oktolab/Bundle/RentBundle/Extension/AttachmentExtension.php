<?php

namespace Oktolab\Bundle\RentBundle\Extension;

/**
 * AttachmentExtension
 */
class AttachmentExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $uploadDir;

    /**
     * Constructor.
     *
     * @param type $container
     */
    public function __construct($container)
    {
        $this->uploadDir = sprintf(
            '%s%s',
            dirname($container->get('router')->getContext()->getBaseUrl()),
            $container->getParameter('oktolab.upload_dir')
        );
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
     * Returns the Upload Path
     *
     * @param string $string
     * @return string
     */
    public function getUploadPath ($attachment)
    {
        //TODO: get UploadPath
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
