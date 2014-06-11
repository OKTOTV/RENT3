<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Oktolab\Bundle\RentBundle\Model\SettingInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of CompanySetting
 *
 * @author rs
 */
class CompanySetting implements SettingInterface
{

    /**
     * @Assert\NotBlank(message = "setting.company_name.notblank")
     *
     * @var string
     */
    private $name;

    /**
     * @Assert\NotBlank(message = "setting.company_adress.notblank")
     *
     * @var string
     */
    private $address;

    /**
     * @Assert\NotBlank(message = "setting.company_postal_code.notblank")
     *
     * @var string
     *
     */
    private $postal_code;

    /**
     * @Assert\NotBlank(message = "setting.company_city.notblank")
     *
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var string
     */
    private $additional_text;

    /**
     * @Assert\Email(message ="setting.company_email.invalid")
     * @var string
     */
    private $email;

    /**
     * 
     * @var string
     */
    private $telnumber;

    /**
     *
     * @param type $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setTelnumber($telnumber)
    {
        $this->telnumber = $telnumber;

        return $this;
    }

    public function getTelnumber()
    {
        return $this->telnumber;
    }

    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setPostalCode($postal_code)
    {
        $this->postal_code = $postal_code;
    }

    public function getPostalCode()
    {
        return $this->postal_code;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setLogo($logo)
    {
        $this->logo = base64_encode($logo);
    }

    public function getLogo()
    {
        if (null !== $this->logo) {
            return base64_decode($this->logo);
        }

        return;
    }

    public function setAdditionalText($addText)
    {
        $this->additional_text = $addText;
    }

    public function getAdditionalText()
    {
        return $this->additional_text;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function toArray()
    {
        $values = array();
        foreach (array('name', 'address', 'postal_code', 'city', 'logo', 'additional_text', 'email', 'telnumber') as $value) {
            $values[$value] = $this->$value;
        }

        return $values;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $values
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\CompanySetting
     */
    public function fromArray(array $values)
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }
}
