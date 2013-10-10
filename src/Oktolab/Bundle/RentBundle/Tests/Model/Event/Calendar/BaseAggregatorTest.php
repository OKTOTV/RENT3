<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\BaseAggregator;

/**
 * Description of BaseAggregator
 *
 * @author meh
 */
class BaseAggregatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System Under Test
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\BaseAggregator
     */
    protected $SUT = null;

    public function setUp()
    {
        parent::setUp();

        $this->SUT = new BaseAggregator();
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Model\Event\Calendar\BaseAggregator', $this->SUT);
    }

    public function testAddARepository()
    {
        // No Repository should be available
        $this->assertSame(null, $this->SUT->getRepository('Set'));

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $this->SUT->addRepository('Set', $repository);
        $this->assertSame($repository, $this->SUT->getRepository('Set'), 'Should get Repository "Set"');
    }

    public function testGetNullOnUnknownRepository()
    {
        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $this->SUT->addRepository('Set', $repository);

        $this->assertSame(null, $this->SUT->getRepository('Item'), 'Should get NULL on Repository "Item"');
    }
}
