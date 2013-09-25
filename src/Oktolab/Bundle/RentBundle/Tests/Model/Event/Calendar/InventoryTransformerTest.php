<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\InventoryTransformer;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Category;

/**
 * Description of InventoryTransformerTest
 *
 * @author meh
 */
class InventoryTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\Inventory
     */
    protected $aggregator = null;

    /**
     * System Under Test
     *
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\InventoryTransformer
     */
    protected $SUT = null;

    public function setUp()
    {
        parent::setUp();

        $this->aggregator = $this->getMock('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\InventoryAggregator');
        $this->SUT = new InventoryTransformer($this->aggregator);
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\InventoryTransformer', $this->SUT);
    }

    public function testGetInventoryReturnsArray()
    {
        $this->aggregator->expects($this->once())->method('getInventory')->will($this->returnValue(array()));
        $this->assertEquals(array(), $this->SUT->getTransformedInventory());
    }

    public function testGetInventoryTransformsItemsInCategories()
    {
        $item = new Item();
        $item->setTitle('Test Item');
        $item->setId(5);

        $category = new Category();
        $category->setTitle('Test Category');
        $category->addItem($item);

        $array = array($category->getTitle() => array($item));
        $this->aggregator->expects($this->once())->method('getInventory')->will($this->returnValue($array));

        $expectedArray = array(array(
            'title'      => $category->getTitle(),
            'objectives' => array(array(
                'objective' => sprintf('%s:%d', $item->getType(), $item->getId()),
                'id'        => $item->getId(),
                'title'     => $item->getTitle(),
            ))
        ));

        $this->assertEquals($expectedArray, $this->SUT->getTransformedInventory());
    }

    public function testGetInventoryTransformsSetsInCategory()
    {
        $set = new Set();
        $set->setTitle('Test Set');
        $set->setId(3);

        $array = array('Sets' => array($set));
        $this->aggregator->expects($this->once())
            ->method('getInventory')
            ->with($this->equalTo(true))
            ->will($this->returnValue($array));

        $expectedArray = array(array(
            'title'      => 'Sets',
            'objectives' => array(array(
                'objective' => sprintf('%s:%d', $set->getType(), $set->getId()),
                'id'        => $set->getId(),
                'title'     => $set->getTitle(),
            ))
        ));

        $this->assertEquals($expectedArray, $this->SUT->getTransformedInventory(true));
    }

    public function testGetInventoryAsJsonReturnsJson()
    {
        $this->aggregator->expects($this->once())->method('getInventory')->will($this->returnValue(array()));
        $this->assertEquals(json_encode(array()), $this->SUT->getInventoryAsJson());
    }

    public function testGetInventoryAsJsonReturnsFullJson()
    {
        $item = new Item();
        $item->setTitle('Test Item As Json Encoded');
        $item->setId(7);

        $category = new Category();
        $category->setTitle('JSON Category');
        $category->addItem($item);

        $array = array($category->getTitle() => array($item));
        $this->aggregator->expects($this->once())->method('getInventory')->will($this->returnValue($array));

        $expectedArray = json_encode(array(array(
            'title'      => $category->getTitle(),
            'objectives' => array(array(
                'objective' => sprintf('%s:%d', $item->getType(), $item->getId()),
                'id'        => $item->getId(),
                'title'     => $item->getTitle(),
            ))
        )));

        $this->assertEquals($expectedArray, $this->SUT->getInventoryAsJson());
    }
}
