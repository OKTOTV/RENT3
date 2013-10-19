<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of CompanySettingType
 *
 * @author rs
 */
class CompanySettingType extends AbstractType
{

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'setting.company.name'))
            ->add('adress', 'text', array('label' => 'setting.company.adress'))
            ->add('plz', 'text', array('label' => 'setting.company.plz'))
            ->add('place', 'text', array('label' => 'setting.company.place'))
            ->add('logo', 'file', array('label' => 'setting.company.logo', 'required' => false))
            ->add(
                'additional_text',
                'textarea',
                array('label' => 'setting.company.additional_text', 'required' => false)
            );
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Oktolab\Bundle\RentBundle\Entity\CompanySetting'));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'oktolab_setting_companytype';
    }
}
