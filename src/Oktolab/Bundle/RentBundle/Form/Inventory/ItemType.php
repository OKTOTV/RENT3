<?php

namespace Oktolab\Bundle\RentBundle\Form\Inventory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oktolab\Bundle\RentBundle\Form\Inventory\AttachmentType;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => 'inventory.item.title'))
            ->add('description', 'textarea', array('label' => 'inventory.item.description'))
            ->add('barcode', 'text', array('label' => 'inventory.item.barcode'))
            ->add(
                'buyDate',
                'date',
                array(
                    'widget' => 'single_text',
                    'required' => false,
                    'empty_value' => '',
                    'label' => 'inventory.item.buydate'
                )
            )
            ->add('serialNumber', 'text', array('label' => 'inventory.item.serialnumber'))
            ->add('vendor', 'text', array('label' => 'inventory.item.vendor'))
            ->add('modelNumber', 'text', array('label' => 'inventory.item.modelnumber'))
            ->add(
                'set',
                'entity',
                array(
                    'class'    => 'OktolabRentBundle:Inventory\Set',
                    'property' => 'title',
                    'required' => false,
                    'label'    => 'inventory.item.set'
                )
            )
            ->add(
                'place',
                'entity',
                array(
                    'class'    => 'OktolabRentBundle:Inventory\Place',
                    'property' => 'title',
                    'required' => true,
                    'label'    => 'inventory.item.place'
                )
            )
            ->add(
                'category',
                'entity',
                array(
                    'class'    => 'OktolabRentBundle:Inventory\Category',
                    'property' => 'title',
                    'required' => false,
                    'label'    => 'inventory.item.category'
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
