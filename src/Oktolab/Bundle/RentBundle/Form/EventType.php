<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', 'textarea', array('required' => false))
            ->add(
                'begin',
                'date',
                array(
                    'widget' => 'single_text',
                    'required' => true,
                    'empty_value' => ''
                )
            )
            ->add(
                'end',
                'date',
                array(
                    'widget' => 'single_text',
                    'required' => true,
                    'empty_value' => ''
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array());
    }

    public function getName()
    {
        return 'oktolabrentbundle_event_form';
    }
}
