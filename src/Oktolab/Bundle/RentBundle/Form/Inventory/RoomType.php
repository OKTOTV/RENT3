<?php

namespace Oktolab\Bundle\RentBundle\Form\Inventory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => 'inventory.room.title'))
            ->add('description', 'textarea', array('label' => 'inventory.room.description'))
            ->add('barcode', 'text', array('label' => 'inventory.room.barcode'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Oktolab\Bundle\RentBundle\Entity\Inventory\Room'));
    }

    public function getName()
    {
        return 'oktolab_bundle_rentbundle_inventory_roomtype';
    }
}
