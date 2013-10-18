<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Doctrine\ORM\EntityManager;
use Oktolab\Bundle\RentBundle\Entity\Setting;

/**
 * Description of Setting
 *
 * @author rs
 */
class SettingService
{
    private $entityManager;

    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    public function get($settingName)
    {
        $KVS = $this->entityManager->getRepository('OktolabRentBundle:Setting')->findOneBy(array('key' => $settingName));
        return unserialize($KVS->getValue());
    }

    public function set($settingName, array $values)
    {
        $KVS = new Setting();
        if ($this->has($settingName)) {
            $KVS = $this->entityManager->getRepository('OktolabRentBundle:Setting')->findOneBy(array('key' => $settingName));
            $KVS->setValue(serialize($values));
        } else {
            $KVS->setKey($settingName);
            $KVS->setValue(serialize($values));
        }
        $this->entityManager->persist($KVS);
        $this->entityManager->flush();
    }

    public function has($settingName)
    {

        $KVS = $this->entityManager->getRepository('OktolabRentBundle:Setting')->findOneBy(array('key' => $settingName));
        return $KVS != null;
    }
}
