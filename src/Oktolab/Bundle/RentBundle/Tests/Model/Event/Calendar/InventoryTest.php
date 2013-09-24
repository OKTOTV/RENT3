<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\Inventory as InventoryCalendar;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;

class InventoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System Under Test
     * @var InventoryCalendar
     */
    protected $SUT = null;

    /**
     * @var Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
     */
    protected $set = null;

    /**
     * @var Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
     */
    protected $item = null;

    public function setup()
    {
        parent::setup();

        $this->SUT = new InventoryCalendar();
        $this->assertInstanceOf('Oktolab\Bundle\RentBundle\Model\Event\Calendar\Inventory', $this->SUT);
    }

    public function testAddARepository()
    {
        // No Repository should be available
        $this->assertSame(null, $this->SUT->getRepository('Set'));

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $this->SUT->addRepository('Set', $repository);
        $this->assertEquals($repository, $this->SUT->getRepository('Set'));
    }

    public function testGetInventoryThrowsErrorOnInvalidRepositoryName()
    {
        $this->setExpectedException('Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException');
        $this->SUT->getInventory('Invalid');
    }

    public function testGetInventoryReturnsSetInArray()
    {
        $repository = $this->trainSetRepositoryToFindAllSets();
        $this->SUT->addRepository('Set', $repository);
        $this->assertContains($this->set, $this->SUT->getInventory('Set'));
    }

    public function testGetInventoryReturnsItemInArray()
    {
        $repository = $this->trainItemRepositoryToFindAllItems();
        $this->SUT->addRepository('Item', $repository);
        $this->assertContains($this->item, $this->SUT->getInventory('Item'));
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
                ->method('find')
                ->with($this->identicalTo(array()), $this->anything(), $this->anything())
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

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
                ->method('find')
                ->with($this->identicalTo(array()), $this->anything(), $this->anything())
                ->will($this->returnValue(array($this->item)));

        return $repository;
    }
}