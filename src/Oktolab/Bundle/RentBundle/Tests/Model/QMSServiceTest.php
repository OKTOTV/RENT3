<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Qms;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Model\QMSService;

/**
 * QMSServiceTest unittests the QMSService class
 *
 * @author rs
 */
class QMSServiceTest extends WebTestCase
{
    public function testCreateOkayQms()
    {
        $item = new Item();
        $qms = new Qms();
        $qms->setStatus(Qms::STATE_OKAY);
        $qms->setItem($item);
        $item->addQms($qms);

        $mockEm = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $mockEm->expects($this->at(0))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(1))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(2))
            ->method('flush')
            ->will($this->returnValue(null));

        $qmsService = new QMSService($mockEm);

        $qmsService->createQMS($qms);

        $this->assertEquals(true, $item->getActive());
        $this->assertCount(1, $item->getQmss());
    }

    public function testCreateFlawQms()
    {
        $item = new Item();
        $qms = new Qms();
        $qms->setStatus(Qms::STATE_FLAW);
        $qms->setItem($item);
        $item->addQms($qms);

        $mockEm = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $mockEm->expects($this->at(0))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(1))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(2))
            ->method('flush')
            ->will($this->returnValue(null));

        $qmsService = new QMSService($mockEm);

        $qmsService->createQMS($qms);

        $this->assertEquals(true, $item->getActive());
        $this->assertCount(1, $item->getQmss());
    }

        public function testCreateDamageQms()
    {
        $item = new Item();
        $qms = new Qms();
        $qms->setStatus(Qms::STATE_DAMAGED);
        $qms->setItem($item);
        $item->addQms($qms);

        $mockEm = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $mockEm->expects($this->at(0))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(1))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(2))
            ->method('flush')
            ->will($this->returnValue(null));

        $qmsService = new QMSService($mockEm);

        $qmsService->createQMS($qms);

        $this->assertEquals(true, $item->getActive());
        $this->assertCount(1, $item->getQmss());
    }

    public function testCreateDestroyedQms()
    {
        $item = new Item();
        $qms = new Qms();
        $qms->setStatus(Qms::STATE_DESTROYED);
        $qms->setItem($item);
        $item->addQms($qms);

        $mockEm = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $mockEm->expects($this->at(0))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(1))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(2))
            ->method('flush')
            ->will($this->returnValue(null));

        $qmsService = new QMSService($mockEm);

        $qmsService->createQMS($qms);

        $this->assertEquals(false, $item->getActive());
        $this->assertCount(1, $item->getQmss());
    }

    public function testCreateLostQms()
    {
        $item = new Item();
        $qms = new Qms();
        $qms->setStatus(Qms::STATE_LOST);
        $qms->setItem($item);
        $item->addQms($qms);

        $mockEm = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $mockEm->expects($this->at(0))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(1))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(2))
            ->method('flush')
            ->will($this->returnValue(null));

        $qmsService = new QMSService($mockEm);

        $qmsService->createQMS($qms);

        $this->assertEquals(false, $item->getActive());
        $this->assertCount(1, $item->getQmss());
    }

    public function testCreateMaintenanceQms()
    {
        $item = new Item();
        $qms = new Qms();
        $qms->setStatus(Qms::STATE_MAINTENANCE);
        $qms->setItem($item);
        $item->addQms($qms);

        $mockEm = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $mockEm->expects($this->at(0))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(1))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(2))
            ->method('flush')
            ->will($this->returnValue(null));

        $qmsService = new QMSService($mockEm);

        $qmsService->createQMS($qms);

        $this->assertEquals(false, $item->getActive());
        $this->assertCount(1, $item->getQmss());
    }

    public function testCreateDiscardedQms()
    {
        $item = new Item();
        $qms = new Qms();
        $qms->setStatus(Qms::STATE_DISCARDED);
        $qms->setItem($item);
        $item->addQms($qms);

        $mockEm = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $mockEm->expects($this->at(0))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(1))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(2))
            ->method('flush')
            ->will($this->returnValue(null));

        $qmsService = new QMSService($mockEm);

        $qmsService->createQMS($qms);

        $this->assertEquals(false, $item->getActive());
        $this->assertCount(1, $item->getQmss());
    }

    public function testCreateFlawAndOkayQms()
    {
        $item = new Item();
        $qms = new Qms();
        $qms->setStatus(Qms::STATE_OKAY);
        $qms->setItem($item);
        $qms2 = new Qms();
        $qms2->setStatus(Qms::STATE_FLAW);
        $qms2->setItem($item);
        $item->addQms($qms);
        $item->addQms($qms2);

        $mockEm = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $mockEm->expects($this->at(0))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(1))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(2))
            ->method('flush')
            ->will($this->returnValue(null));

        $qmsService = new QMSService($mockEm);

        $qmsService->createQMS($qms);

        $this->assertEquals(true, $item->getActive());
        $this->assertCount(2, $item->getQmss());
    }

    public function testItemGetsOkay()
    {
        $item = new Item();
        $item->setActive(false);
        $qms = new Qms();
        $qms->setStatus(Qms::STATE_OKAY);
        $qms->setItem($item);
        $qms2 = new Qms();
        $qms2->setStatus(Qms::STATE_DESTROYED);
        $qms2->setActive(false);
        $qms2->setItem($item);
        $item->addQms($qms);
        $item->addQms($qms2);

        $mockEm = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $mockEm->expects($this->at(0))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(1))
            ->method('persist')
            ->will($this->returnValue(null));
        $mockEm->expects($this->at(2))
            ->method('flush')
            ->will($this->returnValue(null));

        $qmsService = new QMSService($mockEm);

        $qmsService->createQMS($qms);

        $this->assertEquals(true, $item->getActive());
        $this->assertCount(2, $item->getQmss());
    }
}
