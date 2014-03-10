<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oktolab\Bundle\RentBundle\Form\EventType;

/**
 * SeriesEventType for a bunch of events!
 *
 * @author rs
 */
class SeriesEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'textarea', array('label' => 'series_event.description'))
            ->add('begin', 'datetime', array('label' => 'series_event.begin', 'widget' => 'single_text'))
            ->add('end', 'datetime', array('label' => 'series_event.end', 'widget' => 'single_text'))
            ->add('event_begin', 'datetime', array('label' => 'series_event.event_begin', 'widget' => 'single_text'))
            ->add('event_end', 'datetime', array('label' => 'series_event.event_end', 'widget' => 'single_text'))
            ->add('events', 'collection', array('label' => 'series_event.events'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oktolab\Bundle\RentBundle\Entity\SeriesEvent',
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'orb_series_event_form';
    }
}
