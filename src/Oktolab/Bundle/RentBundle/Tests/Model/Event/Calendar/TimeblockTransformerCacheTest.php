<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model\Event\Calendar;

use Doctrine\Common\Cache\ArrayCache;
use Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockTransformer;
use Oktolab\Bundle\RentBundle\EventListener\TimeblockTransformerCacheListener;
use Oktolab\Bundle\RentBundle\Entity\Timeblock;

/**
 * Description of TimeblockTransformerCacheTest
 *
 * @author meh
 */
class TimeblockTransformerCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator
     */
    protected $aggregator = null;

    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockTransformer
     */
    protected $transformer = null;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache = null;

    /**
     * System Under Test
     *
     * @var \Oktolab\Bundle\RentBundle\EventListener\TimeblockTransformerCacheListener
     */
    protected $SUT = null;

    public function setUp()
    {
        parent::setUp();

        $this->cache = new ArrayCache();
        $this->aggregator = $this->getMock('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator');
        $this->transformer = new TimeblockTransformer($this->aggregator, $this->cache);

        $this->SUT = new TimeblockTransformerCacheListener($this->cache);
        $this->assertInstanceOf(
            '\Oktolab\Bundle\RentBundle\EventListener\TimeblockTransformerCacheListener',
            $this->SUT
        );
    }

    /**
     * @dataProvider callbackProvider
     */
    public function testClearCacheOnTimeblockCallbacks($callback)
    {
        $cacheId = sprintf('%s::279', TimeblockTransformer::CACHE_ID); // 2013-10-07 is the 279. day of year
        $this->cache->save($cacheId, array('Timeblock Content'));
        $this->assertTrue($this->cache->contains($cacheId));

        $timeblock = new Timeblock();
        $timeblock->setIntervalBegin(new \DateTime('2013-10-07 00:00'));

        $args = $this->trainLifecycleEventArgs($timeblock);
        $this->SUT->$callback($args);
        $this->assertFalse($this->cache->contains($cacheId));
    }

    public function callbackProvider()
    {
        // I know this is a hack @see InventoryTransformerCacheTest. Alternative: Copy&Paste the Unit-Test?
        return array(array('postPersist'), array('postUpdate'), array('postRemove'));
    }

    /**
     * Returns a Mock for LivecycleEventArgs
     *
     * @param object $object
     * @return \Doctrine\ORM\Event\LifecycleEventArgs|PHPUnit_Framework_MockObject_MockBuilder
     */
    protected function trainLifecycleEventArgs($object)
    {
        $args = $this->getMockBuilder('\Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $args->expects($this->once())->method('getEntity')->will($this->returnValue($object));

        return $args;
    }
}
