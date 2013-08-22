<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Doctrine\ORM\EntityManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Symfony\Component\Validator\Validator;
use Doctrine\Common\Collections\Collection;

class ItemImportManager
{
    private $entityManager;
    private $validator;

    public function __construct(EntityManager $manager, Validator $validator)
    {
        $this->entityManager = $manager;
        $this->validator = $validator;
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
        $handle = fopen($file->getRealPath(), 'r');
        $items = array();
        //parse file with fgetcsv
        while (($data = fgetcsv($handle)) !== FALSE) {
            if ($data[0] == "Titel") {
                continue;
            }
            if (count($data) != 8) {
                return array();
            }

            $item = new Item();
            $item->setTitle($data[0]);
            $item->setDescription($data[1]);
            $item->setBarcode($data[2]);
            $item->setBuyDate(new \DateTime($data[3]));
            $item->setSerialNumber($data[4]);
            $item->setVendor($data[5]);
            $item->setModelNumber($data[6]);

            $place = $this->entityManager->getRepository('OktolabRentBundle:Inventory\Place')->findOneByTitle($data[7]);
            if (!$place) {
                return array();
            }
            $item->setPlace($place);
            $items[] = $item;
        }
        fclose($handle);

        return $items;
    }

    /**
     * Validates the items like implemented in entity
     *
     * returns true if validation is succesfull, false otherwise.
     *
     * @param array $items
     * @return bool
     */
    public function validate(array $items)
    {
        foreach($items as $item) {
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
     * @return bool
     */
    public function persistItems(array $items)
    {
        foreach($items as $item) {
            $this->entityManager->persist($item);
        }
    }

    /**
     * Persists an file
     *
     * @param \SplFileInfo $file
     * @return boolean
     */
    public function persistItemCsv(\SplFileInfo $file)
    {
        $items = $this->parse($file);
        if ($this->validate($items)) {
            $this->persistItems($items);
            return true;
        }
        return false;
    }
}