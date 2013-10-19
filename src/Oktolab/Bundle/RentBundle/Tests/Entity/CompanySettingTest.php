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
            $this->SUT->setWithArray(array())
        );
    }

    /**
     * @test
     */
    public function setAsArray()
    {
        $settings = array(
            'name'              => 'Oktolab GmbH',
            'adress'            => '123, Example Street',
            'plz'               => 'postal code',
            'place'             => 'austria',
            'logo'              => null,
            'additional_text'   => null,
        );

        $this->SUT->setWithArray($settings);
        $this->assertSame($settings['name'], $this->SUT->getName());
        $this->assertSame($settings['adress'], $this->SUT->getAdress());
        $this->assertSame($settings['plz'], $this->SUT->getPlz());
        $this->assertSame($settings['place'], $this->SUT->getPlace());
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
            'adress'            => '123, Example Street',
            'plz'               => 'postal code',
            'place'             => 'austria',
            'logo'              => null,
            'additional_text'   => null,
        );

        $this->SUT->setName('Oktolab GmbH');
        $this->SUT->setAdress('123, Example Street');
        $this->SUT->setPlz('postal code');
        $this->SUT->setPlace('austria');

        $this->assertEquals($expected, $this->SUT->getValueArray());
    }
}
