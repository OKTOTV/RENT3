<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventObjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type');
        $builder->add('object');
        $builder->add('scanned', 'checkbox', array('attr' => array('class' => 'scanner'), 'required' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Oktolab\Bundle\RentBundle\Entity\EventObject'));
    }

    public function getName()
    {
        return 'event_object';
    }
}
