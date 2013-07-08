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
            ->add('buyDate')
            ->add('serialNumber')
            ->add('vendor')
            ->add('modelNumber')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oktolab\Bundle\RentBundle\Entity\Inventory\Item'
        ));
    }

    public function getName()
    {
        return 'oktolab_bundle_rentbundle_inventory_itemtype';
    }
}
