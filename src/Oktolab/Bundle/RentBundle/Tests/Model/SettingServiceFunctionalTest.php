<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Tests\WebTestCase;

/**
 * Setting Service Functional Tests
 *
 * @author meh
 */
class SettingServiceFunctionalTest extends WebTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(array('Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\SettingFixture'));
    }

    /**
     * @test
     */
    public function getValue()
    {
        $oSettings = $this->getContainer()->get('oktolab.setting');
        $this->assertSame(array('bar', 'baz'), $oSettings->get('foo'));
    }

    /**
     * @test
     */
    public function getValueReturnsDefault()
    {
        $oSettings = $this->getContainer()->get('oktolab.setting');
        $this->assertSame('foobarbaz', $oSettings->get('non_existing_key', 'foobarbaz'));
    }

    /**
     * @test
     */
    public function setValueOverridesExistingSetting()
    {
        $oSettings = $this->getContainer()->get('oktolab.setting');
        $oSettings->set('foo', array('new value', 'foobarbaz'));
        $this->assertSame(array('new value', 'foobarbaz'), $oSettings->get('foo'));
    }

    /**
     * @test
     */
    public function setValueAddsNewSetting()
    {
        $oSettings = $this->getContainer()->get('oktolab.setting');
        $oSettings->set('new_key', array('new value'));

        $this->assertSame(array('new value'), $oSettings->get('new_key'));
        $this->assertSame(array('bar', 'baz'), $oSettings->get('foo'));
    }

    /**
     * @test
     */
    public function hasReturnsTrue()
    {
        $oSettings = $this->getContainer()->get('oktolab.setting');
        $this->assertTrue($oSettings->has('foo'));
    }

    /**
     * @test
     */
    public function hasReturnsFalse()
    {
        $oSettings = $this->getContainer()->get('oktolab.setting');
        $this->assertFalse($oSettings->has('non_existing_key'));
    }

    /**
     * @test
     */
    public function deleteKey()
    {
        $oSettings = $this->getContainer()->get('oktolab.setting');
        $oSettings->delete('foo');

        $this->assertFalse($oSettings->has('foo'));
    }
}
