<?php

namespace Oktolab\Bundle\RentBundle\Tests\Entity;

use Oktolab\Bundle\RentBundle\Entity\CompanySetting;

class CompanySettingTest extends \PHPUnit_Framework_TestCase
{

    /**
     * System Under Test
     * @var \Oktolab\Bundle\RentBundle\Entity\CompanySetting
     */
    protected $SUT = null;

    public function setUp()
    {
        parent::setUp();

        $this->SUT = new CompanySetting();
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\CompanySetting', $this->SUT);
    }

    /**
     * @test
     */
    public function setAsArrayReturnsAnInstance()
    {
        $this->assertInstanceOf(
            '\Oktolab\Bundle\RentBundle\Entity\CompanySetting',
            $this->SUT->fromArray(array())
        );
    }

    /**
     * @test
     */
    public function setNameReturnsAnInstance()
    {
        $this->assertInstanceOf(
            '\Oktolab\Bundle\RentBundle\Entity\CompanySetting',
            $this->SUT->setName('Oktolab')
        );
    }

    /**
     * @test
     */
    public function setAsArray()
    {
        $settings = array(
            'name'              => 'Oktolab GmbH',
            'address'           => '123, Example Street',
            'postal_code'       => '123A5',
            'city'              => 'austria',
            'logo'              => null,
            'additional_text'   => null,
        );

        $this->SUT->fromArray($settings);
        $this->assertSame($settings['name'], $this->SUT->getName());
        $this->assertSame($settings['address'], $this->SUT->getAddress());
        $this->assertSame($settings['postal_code'], $this->SUT->getPostalCode());
        $this->assertSame($settings['city'], $this->SUT->getCity());
        $this->assertEquals($settings['logo'], $this->SUT->getLogo());
        $this->assertSame($settings['additional_text'], $this->SUT->getAdditionalText());
    }

    /**
     * @test
     */
    public function toArray()
    {
        $expected = array(
            'name'              => 'Oktolab GmbH',
            'address'           => '123, Example Street',
            'postal_code'       => '123A5',
            'city'              => 'austria',
            'logo'              => null,
            'additional_text'   => null,
            'email'             => null,
            'telnumber'         => null
        );

        $this->SUT->setName($expected['name']);
        $this->SUT->setAddress($expected['address']);
        $this->SUT->setPostalCode($expected['postal_code']);
        $this->SUT->setCity($expected['city']);

        $this->assertEquals($expected, $this->SUT->toArray());
    }
}
