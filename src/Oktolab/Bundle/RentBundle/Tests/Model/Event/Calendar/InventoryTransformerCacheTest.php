<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model\Event\Calendar;

use Doctrine\Common\Cache\ArrayCache;
use Oktolab\Bundle\RentBundle\Model\Event\Calendar\InventoryTransformer;
use Oktolab\Bundle\RentBundle\EventListener\InventoryTransformerCacheListener;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Category;

/**
 * Description of InventoryTransformerCacheTest
 *
 * @author meh
 */
class InventoryTransformerCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\Inventory
     */
    protected $aggregator = null;

    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\InventoryTransformer
     */
    protected $transformer = null;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache = null;

    /**
     * System Under Test
     *
     * @var \Oktolab\Bundle\RentBundle\EventListener\InventoryTransformerCacheListener
     */
    protected $SUT = null;

    public function setUp()
    {
        parent::setUp();

        $this->aggregator = $this->getMock('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\InventoryAggregator');
        $this->cache = new ArrayCache();

        $this->transformer = new InventoryTransformer($this->aggregator, $this->cache);
        $this->SUT = new InventoryTransformerCacheListener($this->cache);
        $this->assertInstanceOf(
            '\Oktolab\Bundle\RentBundle\EventListener\InventoryTransformerCacheListener',
            $this->SUT
        );
    }

    public function testCanAddResultToCache()
    {
        $this->assertFalse($this->cache->contains(InventoryTransformer::CACHE_ID));

        $category = new Category();
        $category->setTitle('Test Category');
        $category->addItem($item = $this->prepareItem());

        $array = array($category->getTitle() => array($item));
        $this->aggregator->expects($this->once())->method('getInventory')->will($this->returnValue($array));

        $inventory = $this->transformer->getTransformedInventory();
        $this->assertTrue($this->cache->contains(InventoryTransformer::CACHE_ID));
        $this->assertSame($inventory, $this->cache->fetch(InventoryTransformer::CACHE_ID));
    }

    /**
     * @depends testCanAddResultToCache
     * @dataProvider callbackProvider
     */
    public function testResultCacheWillBeDeletedOnCategoryCallbacks($callback)
    {
        $this->cache->save(InventoryTransformer::CACHE_ID, array('content_here'));
        $this->assertTrue($this->cache->contains(InventoryTransformer::CACHE_ID));

        $category = new Category();
        $category->setTitle('Test Category');

        $args = $this->getMockBuilder('\Doctrine\ORM\Event\LifecycleEventArgs')
                ->disableOriginalConstructor()
                ->getMock();
        $args->expects($this->once())->method('getEntity')->will($this->returnValue($category));

        $this->SUT->$callback($args);
        $this->assertFalse($this->cache->contains(InventoryTransformer::CACHE_ID));
    }

    public function callbackProvider()
    {
        // I know this is a hack (it's 3:00 AM ...). Alternative: Copy&Paste the Unit-Test?
        return array(array('postPersist'), array('postUpdate'), array('postRemove'));
    }

    /**
     * Returns a prepared Item.
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\Inventory\Item
     */
    protected function prepareItem()
    {
        $item = new Item();
        $item->setId(5)
            ->setTitle('Test Item')
            ->setDescription('Used for InventoryTransformerCacheTests');

        return $item;
    }
}
