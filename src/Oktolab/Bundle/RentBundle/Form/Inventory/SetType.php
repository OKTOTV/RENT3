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
            ->add('searchItems', 'text', array('mapped' => false))
            ->add('barcode')
            ->add(
                'itemsToAdd',
                'collection',
                array(
                    'type' => new SetAddItemType(),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => false,
                    'mapped' => false,
                    'attr' => array('hidden' => 'true')
                )
            );
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
        return 'oktolab_rentbundle_inventory_set';
    }
}
