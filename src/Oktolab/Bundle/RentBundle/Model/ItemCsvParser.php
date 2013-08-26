<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Model\ItemParserInterface;
use Doctrine\ORM\EntityManager;


class ItemCsvParser implements ItemParserInterface
{
    private $entityManager;
    private $headers = array(
                'title',
                'description',
                'barcode',
                'buydate',
                'serialnumber',
                'vendor',
                'modelnumber',
                'place');


    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    /**
     * Validates given csv and checks for missing columns
     * returns true if valid, false otherwise
     *
     * @param SplFileInfo $file
     * @return bool
     */
    public function validateFile(\SplFileInfo $file) {
        $handle = fopen($file->getRealPath(), 'r');

        if ( ($data = fgetcsv($handle)) !== false ) {

            //todo: translate headers

            if (count($data) == 8) {
                foreach ($this->headers as $header) {
                    if (!in_array($header, $data)) {
                        return false;
                    }
                }

                fclose($handle);
                return true;
            }
        }
        fclose($handle);
        return false;
    }

    /**
     * Parses given csv into items
     * Will ignore first line if it contains typical values like 'Titel/Title' or 'Beschreibung/description'
     *
     * @param SplFileInfo $file
     * @return array
     */
    public function parse(\SplFileInfo $file)
    {
        $handle = fopen($file->getRealPath(), 'r');
        $items = array();
        $headers = array();

        //parse file with fgetcsv
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (in_array('title', $data)) {
                $headers = $this->getOrderOfHeaders($data);
                continue;
            }

            $item = new Item();
            $item->setTitle($data[$headers['title']]);
            $item->setDescription($data[$headers['description']]);
            $item->setBarcode($data[$headers['barcode']]);
            $item->setBuyDate(new \DateTime($data[$headers['buydate']]));
            $item->setSerialNumber($data[$headers['serialnumber']]);
            $item->setVendor($data[$headers['vendor']]);
            $item->setModelNumber($data[$headers['modelnumber']]);

            $place = $this->entityManager->getRepository('OktolabRentBundle:Inventory\Place')->findOneByTitle($data[$headers['place']]);
            if (!$place) {
                return array();
            }
            $item->setPlace($place);

            $items[] = $item;
        }
        fclose($handle);

        return $items;
    }

    private function getOrderOfHeaders(array $csvRows) {
        $rows = array(
            'title' => 0,
            'description' => 1,
            'barcode' => 2,
            'buydate' => 3,
            'serialnumber' => 4,
            'vendor' => 5,
            'modelnumber' => 6,
            'place' => 7
        );
        //TODO: autotranslate row headers.
        for ($i = 0; $i < count($csvRows); $i++) {
            $csvRows[$i] = strtolower($csvRows[$i]);
        }

        foreach ($rows as $key => $value) {
            $header = false;
            $header = array_search($key, $csvRows);
            if ($header === false) {
                die(var_dump($header));
                throw new \Exception(sprintf('Headername "%s" in csv not found.', $key));
            }
            $rows[$key] = $header;
        }

        return $rows;
    }
}