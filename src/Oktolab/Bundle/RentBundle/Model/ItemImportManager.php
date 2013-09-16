<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Doctrine\ORM\EntityManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Symfony\Component\Validator\Validator;
use Oktolab\Bundle\RentBundle\Model\ItemCsvParser;
use Oktolab\Bundle\RentBundle\Model\ItemParserInterface;

class ItemImportManager
{
    private $entityManager;
    private $validator;
    private $parser;
    private $csvParser;
    private $mode;

    public function __construct(EntityManager $manager, Validator $validator, ItemCsvParser $csvParser)
    {
        $this->entityManager = $manager;
        $this->validator = $validator;
        $this->csvParser = $csvParser;
    }

    /**
     * Sets the parsermode and defaultparser
     * Current available options: csv
     * throws an exception if mode doesn't exist
     *
     * @param string $mode
     */
    public function setParserMode($mode)
    {

        $haystack = array('csv');

        if (in_array($mode, $haystack)) {
            $this->mode = $mode;
            switch ($mode) {
                case 'csv':
                    $this->parser = $this->csvParser;
            }
        } else {
            throw new \Exception("No Valid Parsermode for ItemImportManager given.");
        }
    }

    /**
     * Returns current Parsermode.
     * @return string
     */
    public function getParserMode()
    {
        return $this->mode;
    }

    /**
     * Validates the items like implemented in entity
     *
     * returns true if validation is succesfull
     *
     * @param array $items
     * @return bool
     */
    public function validateItems(array $items)
    {
        foreach ($items as $item) {
            if (count($this->validator->validate($item))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Persists an array of items.
     *
     * @param array $items
     */
    public function persistItems(array $items)
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            foreach ($items as $item) {
                $this->entityManager->persist($item);
            }
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();

        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            $this->entityManager->close();
            throw $e;
        }
    }

    /**
     * Validates depending on Parsermode the given file
     * Throws an exception if parsermode is invalid
     *
     * @param \SplFileInfo $file
     * @return type
     * @throws \Exception
     */
    public function validateFile(\SplFileInfo $file)
    {
        if ($this->mode === null) {
            throw new \Exception("No Valid Parsermode for ItemImportManager given. Can't validate file");
        }
        return $this->parser->validateFile($file);
    }

    /**
     * Reads file and parses it into items
     * returns an array of unchecked items
     *
     *
     * @param SplFileInfo $file
     * @return array items
     */
    public function parse(\SplFileInfo $file)
    {
        if ($this->mode === null) {
            throw new \Exception("No Valid Parsermode for ItemImportManager given. Can't parse file.");
        }
        return $this->parser->parse($file);
    }
}
