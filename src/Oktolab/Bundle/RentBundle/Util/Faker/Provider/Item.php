<?php

namespace Oktolab\Bundle\RentBundle\Util\Faker\Provider;

use Faker\Provider\Base;

/**
 *  Provides Item fake data
 */
class Item extends Base
{
    /**
     *  Generates a random title for Items
     *  @return string
     */
    public function itemTitle()
    {
        $vendors = array('JVC', 'Sony', 'Yamaha', 'Canon', 'Blackmagic', 'Aja');
        $models  = array('A57', 'A55', 'GY-HM650U', 'GY-HMQ10U', 'GY-HM600U', 'EF', 'MFT');

        return sprintf('%s %s', $this->generator->randomElement($vendors), $this->generator->randomElement($models));
    }

    /**
     * Generates a random barcode for Items
     * @return string
     */
    public function itemBarcode()
    {
        return strtoupper(substr(md5($this->generator->randomNumber(3)), 0, 7));
    }

    /**
     * Returns a random vendor for Item
     * @return string
     */
    public function itemVendor()
    {
        return $this->generator->randomElement(array('JVC', 'Sony', 'Yamaha', 'Canon', 'Blackmagic', 'Aja'));
    }
}