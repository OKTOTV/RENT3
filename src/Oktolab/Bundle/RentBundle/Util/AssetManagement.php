<?php

namespace Oktolab\Bundle\RentBundle\Util;

use Composer\Script\CommandEvent;

/**
 * Checks (and probably creates) the Upload and Media Folders for Media-Assets
 *
 * @author meh
 */
class AssetManagement
{

    /**
     * Checks for directories
     *
     * @param Event $event
     */
    public static function createUploadFolder(CommandEvent $event)
    {
        $options = static::getOptions($event);
        $uploadDir = $options['upload-dir'];

        echo sprintf('Checking the upload-dir %s %s', $uploadDir, PHP_EOL);
        if (!is_dir($uploadDir)) {
            static::createDirectory($uploadDir);
        }
    }

    /**
     * Returns Options merged with default Options
     *
     * @param CommandEvent $event
     * @return array
     */
    protected static function getOptions(CommandEvent $event)
    {
        $composerOptions = $event->getComposer()->getPackage()->getExtra();
        return array_merge(
            array('upload-dir' => 'web/uploads'),
            $composerOptions['oktolab-parameters']
        );
    }

    /**
     * Creates the specified directory.
     *
     * @param string $directory
     */
    protected static function createDirectory($directory)
    {
        if(!mkdir($directory)) {
            echo sprintf('ERROR: Unable to create directory %s %s', $directory, PHP_EOL);
            return;
        }

        echo sprintf('Successfully created directory %s %s', $directory, PHP_EOL);
    }
}
