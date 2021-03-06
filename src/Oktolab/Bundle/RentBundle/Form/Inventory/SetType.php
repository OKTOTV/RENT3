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
            ->add('title', 'text', array('label' => 'inventory.set.title'))
            ->add('description', 'textarea', array('label' => 'inventory.set.description'))
            ->add('barcode', 'text', array('label' => 'inventory.set.barcode'))
            ->add(
                'items',
                'collection',
                array(
                    'type'          => 'entity',
                    'options'       => array('class' => 'OktolabRentBundle:Inventory\Item', 'property' => 'id'),
                    'allow_add'     => true,
                    'allow_delete'  => true,
                )
            )
            ->add(
                'place',
                'entity',
                array(
                    'class'    => 'OktolabRentBundle:Inventory\Place',
                    'property' => 'title',
                    'required' => true,
                    'label'    => 'inventory.set.place'
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => 'Oktolab\Bundle\RentBundle\Entity\Inventory\Set',
                'cascade_validation' => true,
            )
        );
    }

    public function getName()
    {
        return 'oktolab_rentbundle_inventory_set';
    }
}
