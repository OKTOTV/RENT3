<?php

namespace Oktolab\Bundle\RentBundle\Form\Inventory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description', 'textarea')
            ->add('barcode')
            ->add(
                'buyDate',
                'date',
                array(
                    'widget' => 'single_text',
                    'required' => false,
                    'empty_value' => ''
                )
            )
            ->add('serialNumber')
            ->add('vendor')
            ->add('modelNumber')
            ->add(
                'set',
                'entity',
                array(
                    'class' => 'OktolabRentBundle:Inventory\Set',
                    'property' => 'title',
                    'required' => false
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Oktolab\Bundle\RentBundle\Entity\Inventory\Item'
            )
        );
    }

    public function getName()
    {
        return 'oktolab_bundle_rentbundle_inventory_itemtype';
    }
}
