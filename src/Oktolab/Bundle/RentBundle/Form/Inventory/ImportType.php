<?php

namespace Oktolab\Bundle\RentBundle\Form\Inventory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Oktolab\Bundle\RentBundle\Form\Inventory\ItemType;

class ImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'items',
                'collection',
                array(
                    'type'          => new ItemType(),
                    'allow_add'     => true,
                    'attr'          => array('style'=>'display:none;')
                )
            );
    }

//    public function setDefaultOptions(OptionsResolverInterface $resolver)
//    {
//        $resolver->setDefaults(
//            array(
//                'data_class' => 'Oktolab\Bundle\RentBundle\Entity\Inventory\Item'
//            )
//        );
//    }

    public function getName()
    {
        return 'oktolab_bundle_rentbundle_inventory_import';
    }
}
