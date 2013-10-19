<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Setting;

/**
 * SettingFixture
 *
 * @author meh
 */
class SettingFixture extends AbstractFixture
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $setting = new Setting();
        $setting->setKey('foo')
                ->setValue(serialize(array('bar', 'baz')));

        $manager->persist($setting);
        $manager->flush();
    }
}
