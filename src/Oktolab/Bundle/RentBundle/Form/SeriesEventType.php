<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * SeriesEventType for a bunch of events!
 *
 * @author rs
 */
class SeriesEventType extends AbstractType
{
    private $repetitions = array();

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'end',
                'datetime',
                array(
                    'label'     => 'series_event.end',
                    'widget'    => 'single_text',
                )
            )
            ->add(
                'contact',
                'entity',
                array(
                    'class'         => 'OktolabRentBundle:Contact',
                    'label'         => 'admin.contact',
                    'empty_value'   => ''
                )
            )
            ->add(
                'costunit',
                'entity',
                array(
                    'class'         => 'OktolabRentBundle:CostUnit',
                    'label'         => 'admin.costunit',
                    'empty_value'   => ''
                )
            )
            ->add(
                'repetition',
                'choice',
                array(
                    'choices'   => $this->repetitions,
                    'label'     => 'series_event.repetition'
                )
            )
            ->add(
                'event_begin',
                'datetime',
                array(
                    'label' => 'series_event.event_begin',
                    'widget' => 'single_text',
                    'required'      => false,
                )
            )
            ->add(
                'event_end',
                'datetime',
                array(
                    'label' => 'series_event.event_end',
                    'widget' => 'single_text',
                    'required'      => false,
                )
            )
            ->add(
                'objects',
                'collection',
                array(
                    'type'          => new EventObjectType(),
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'label'         => 'event.objects',
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oktolab\Bundle\RentBundle\Entity\SeriesEvent',
            'cascade_validation' => true
        ));
    }

    public function __construct($repetitions = array(7 => 7, 14 => 14, 21 => 21, 28 => 28))
    {
        $this->repetitions = $repetitions;
    }

    public function getName()
    {
        return 'orb_series_event_form';
    }
}
