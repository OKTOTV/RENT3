<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CostUnitType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $hubTransformer = $options['hubTransformer'];
        $mainContactChoices = $options['mainContactChoices'];

        $builder
            ->add('name', 'text', array('label' => 'admin.costunit.name'))
            ->add('abbreviation', 'text', array('label' => 'admin.costunit.abbreviation', 'required' => false))
            ->add(
                'mainContact',
                'entity',
                array(
                    'class'     => 'OktolabRentBundle:Contact',
                    'choices'   => $mainContactChoices,
                    'property'  => 'name',
                    'label'     => 'admin.costunit.mainContact',
                    'required'  => false,
                )
            )
            ->add(
                $builder->create(
                    'contacts',
                    'text',
                    array(
                        'label' => 'admin.costunit.contact'
                    )
                )->addModelTransformer($hubTransformer)
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oktolab\Bundle\RentBundle\Entity\CostUnit'
        ));
        $resolver->setRequired(array(
            'hubTransformer',
            'mainContactChoices'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oktolab_bundle_rentbundle_costunit';
    }
}
