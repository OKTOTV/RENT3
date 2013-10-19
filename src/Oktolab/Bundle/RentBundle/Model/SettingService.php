<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Doctrine\ORM\EntityManager;
use Oktolab\Bundle\RentBundle\Entity\Setting;

/**
 * Description of Setting Service
 *
 * @author rs
 */
class SettingService
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em = null;

    /**
     * Constructor.
     *
     * @param \Doctrine\ORM\EntityManager $manager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Returns the setting from the database.
     *
     * @TODO: Use Doctrine Query-Cache && Doctrine Result-Cache
     * @TODO: Hydrate as SingleScalarResult for Value
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $setting = $this->em->getRepository('OktolabRentBundle:Setting')
                ->findOneBy(array('key' => $key));

        return null !== $setting ? unserialize($setting->getValue()) : $default;
    }

    /**
     * Sets values with the identifier key and persists it to database
     *
     * @param string $key
     * @param array  $values
     */
    public function set($key, array $values)
    {
        $result = $this->em->getRepository('OktolabRentBundle:Setting')
                ->findOneBy(array('key' => $key));

        $setting = (null !== $result) ? $result : new Setting();

        $setting->setKey($key);
        $setting->setValue(serialize($values));

        $this->em->persist($setting);
        $this->em->flush();
    }

    /**
     * Returns true if a setting with key-identifier is stored in database.
     *
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        return null !== $this->em->getRepository('OktolabRentBundle:Setting')->findOneBy(array('key' => $key));
    }

    /**
     * Deletes setting from database.
     *
     * @param string $key
     */
    public function delete($key)
    {
        $result = $this->em->getRepository('OktolabRentBundle:Setting')
                ->findOneBy(array('key' => $key));

        if (null !== $result) {
            $this->em->remove($result);
            $this->em->flush();
        }
    }
}
