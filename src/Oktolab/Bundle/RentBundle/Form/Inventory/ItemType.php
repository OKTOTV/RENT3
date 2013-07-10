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
            //->add('buyDate', 'date', array('widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => array('min' => '1970-01-01', 'max' => '2030-12-31')))
            ->add('buyDate', 'date', array('widget' => 'single_text'))
            ->add('serialNumber')
            ->add('vendor')
            ->add('modelNumber');
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
