<?php

namespace Oktolab\Bundle\RentBundle\Form\Inventory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('label' => 'inventory.category.title'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Oktolab\Bundle\RentBundle\Entity\Inventory\Category'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oktolab_bundle_rentbundle_inventory_categorytype';
    }
}
