<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\InventoryAggregator;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Category;

class InventoryAggregatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System Under Test
     * @var InventoryCalendar
     */
    protected $SUT = null;

    /**
     * @var \Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
     */
    protected $set = null;

    /**
     * @var \Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
     */
    protected $item = null;

    /**
     * @var \Oktolab\Bundle\RentBundle\Entity\Inventory\Category
     */
    protected $category = null;

    public function setup()
    {
        parent::setup();

        $this->SUT = new InventoryAggregator();
        $this->assertInstanceOf('Oktolab\Bundle\RentBundle\Model\Event\Calendar\InventoryAggregator', $this->SUT);
    }

    public function testGetObjectivesThrowsErrorOnInvalidRepositoryName()
    {
        $this->setExpectedException('Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException');
        $this->SUT->getObjectives('Invalid');
    }

    public function testGetObjectivesReturnsSetInArray()
    {
        $repository = $this->trainSetRepositoryToFindAllSets();
        $this->SUT->addRepository('Set', $repository);
        $this->assertContains($this->set, $this->SUT->getObjectives('Set'));
    }

    public function testGetObjectivesReturnsItemInArray()
    {
        $repository = $this->trainItemRepositoryToFindAllItems();
        $this->SUT->addRepository('Item', $repository);
        $this->assertContains($this->item, $this->SUT->getObjectives('Item'));
    }

    public function testGetCategoriesReturnsCategoryInArray()
    {
        $repository = $this->trainCategoryRepositoryToFindAllCategories();
        $this->SUT->addRepository('Category', $repository);
        $this->assertContains($this->category, $this->SUT->getCategories());
    }

    public function testGetCategoriesThrowsExceptionIfRepositoryIsNotSet()
    {
        $this->setExpectedException('Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException');
        $this->SUT->getCategories();
    }

    public function testGetInventoryReturnsCorrectArrayIndexes()
    {
        $repository = $this->trainCategoryRepositoryToFindAllCategories();
        $itemRepository = $this->trainItemRepositoryToFindAllItems();
        $this->SUT->addRepository('Category', $repository);
        $this->SUT->addRepository('Item', $itemRepository);
        $this->assertArrayHasKey($this->category->getTitle(), $this->SUT->getInventory());
    }

    public function testGetInventoryReturnsItemsInCategory()
    {
        $repository = $this->trainCategoryRepositoryToFindAllCategories();
        $itemRepository = $this->trainItemRepositoryToFindAllItems();
        $this->SUT->addRepository('Category', $repository);
        $this->SUT->addRepository('Item', $itemRepository);
        $item = new Item();
        $this->category->addItem($item);

        $inventory = $this->SUT->getInventory();
        $this->assertArrayHasKey($this->category->getTitle(), $inventory);
        $this->assertContains($item, $inventory[$this->category->getTitle()]);
    }

    public function testGetInventoryReturnsSetsAsCategory()
    {
        $setRepository = $this->trainSetRepositoryToFindAllSets();
        $categoryRepository = $this->trainCategoryRepositoryToFindAllCategories();
        $itemRepository = $this->trainItemRepositoryToFindAllItems();
        $this->SUT->addRepository('Set', $setRepository);
        $this->SUT->addRepository('Category', $categoryRepository);
        $this->SUT->addRepository('Item', $itemRepository);

        $inventory = $this->SUT->getInventory(true);
        $this->assertArrayHasKey('Sets', $inventory);
        $this->assertCount(1, $inventory['Sets']);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function trainSetRepositoryToFindAllSets()
    {
        $this->set = new Set();
        $this->set->setTitle('Test Set');

        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
                ->method('findBy')
                ->will($this->returnValue(array($this->set)));

        return $repository;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function trainItemRepositoryToFindAllItems()
    {
        $this->item = new Item();
        $this->item->setTitle('Test Item');

        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
                ->method('findBy')
                ->will($this->returnValue(array($this->item)));

        return $repository;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function trainCategoryRepositoryToFindAllCategories()
    {
        $item = new Item();
        $item->setTitle('Test Item');

        $this->category = new Category();
        $this->category->setTitle('Test Category');
        $this->category->addItem($item);

        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
                ->method('findBy')
                ->will($this->returnValue(array($this->category)));

        return $repository;
    }
}
