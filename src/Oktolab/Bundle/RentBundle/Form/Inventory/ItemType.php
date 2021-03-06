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
            ->add('barcode', 'text', array('label' => 'inventory.item.barcode'))            
            ->add('description', 'textarea', array('label' => 'inventory.item.description', 'required' => false ))
            ->add('notice', 'textarea', array('label' => 'inventory.item.notice', 'required' => false ))
            ->add('origin_value', 'text', array('label' => 'inventory.item.origin_value', 'required' => false))
            ->add('daily_rent', 'text', array('label' => 'inventory.item.daily_rent', 'required' => false))

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
            ->add('serialNumber', 'text', array('label' => 'inventory.item.serialnumber', 'required' => false))
            ->add('vendor', 'text', array('label' => 'inventory.item.vendor', 'required' => false))
            ->add('modelNumber', 'text', array('label' => 'inventory.item.modelnumber', 'required' => false))
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
