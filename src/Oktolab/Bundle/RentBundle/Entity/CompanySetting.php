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
     * @var string
     * @Assert\NotBlank(message = "setting.name.notblank")
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank(message = "setting.adress.notblank")
     */
    private $adress;

    /**
     * @var string
     * @Assert\NotBlank(message = "setting.plz.notblank")
     */
    private $plz;

    /**
     * @var string
     * @Assert\NotBlank(message = "setting.place.notblank")
     */
    private $place;

    private $logo;
    
    private $additional_text;

    public function setName($name)
    {
       $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setAdress($adress)
    {
        $this->adress = $adress;
    }

    public function getAdress()
    {
        return $this->adress;
    }

    public function setPlz($plz)
    {
        $this->plz = $plz;
    }

    public function getPlz()
    {
        return $this->plz;
    }

    public function setPlace($place)
    {
        $this->place = $place;
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function setLogo($logo)
    {
        $this->logo = base64_encode($logo);
    }

    public function getLogo()
    {
        return base64_decode($this->logo);
    }

    public function setAdditionalText($addText)
    {
        $this->additional_text = $addText;
    }

    public function getAdditionalText()
    {
        return $this->additional_text;
    }

    public function setWithArray(array $values)
    {
        $this->name = $values['name'];
        $this->adress = $values['adress'];
        $this->plz = $values['plz'];
        $this->place = $values['place'];
        $this->logo = $values['logo'];
        $this->additional_text = $values['additional_text'];
    }

    public function getValueArray()
    {
        $value = array();
        $value['name'] = $this->name;
        $value['adress'] = $this->adress;
        $value['plz'] = $this->plz;
        $value['place'] = $this->place;
        $value['logo'] = $this->logo;
        $value['additional_text'] = $this->additional_text;

        return $value;
    }
}
