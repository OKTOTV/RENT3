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
            ->add('address', 'text', array('label' => 'setting.company.address'))
            ->add('postal_code', 'text', array('label' => 'setting.company.postal_code'))
            ->add('city', 'text', array('label' => 'setting.company.city'))
            ->add(
                'additional_text',
                'textarea',
                array('label' => 'setting.company.additional_text', 'required' => false)
            )
            ->add('email', 'text', array('label' => 'setting.company.email'))
            ->add('telnumber', 'text', array('label' => 'setting.company.telnumber'));
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
