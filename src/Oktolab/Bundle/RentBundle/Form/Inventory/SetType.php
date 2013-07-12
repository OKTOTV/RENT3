<?php

namespace Oktolab\Bundle\RentBundle\Form\Inventory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description', 'textarea')
            ->add('searchItems', 'text', array('mapped' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Oktolab\Bundle\RentBundle\Entity\Inventory\Set'
            )
        );
    }

    public function getName()
    {
        return 'oktolab_bundle_rentbundle_inventory_settype';
    }
}
