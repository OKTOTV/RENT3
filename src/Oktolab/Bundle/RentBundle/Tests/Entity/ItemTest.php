<?php

namespace Oktolab\Bundle\RentBundle\Tests\Entity;

use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    private $item = null;

    public function setUp()
    {
        $this->item = new Item();
    }

    public function testWarrantyIsVoid()
    {
        $this->item->setWarrantyDate(new \DateTime('-7 days'));
        $this->assertTrue($this->item->isWarrantyVoid());
    }

    public function testWarrantyIsValid()
    {
        $this->item->setWarrantyDate(new \DateTime('+3 days'));
        $this->assertFalse($this->item->isWarrantyVoid());
    }
}
