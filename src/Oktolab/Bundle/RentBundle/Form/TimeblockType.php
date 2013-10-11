<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oktolab\Bundle\RentBundle\Form\DataTransformer\WeekdaysToArrayTransformer;

class TimeblockType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //TODO: Translationservice -.-
        $translator = $options['translator'];

        $weekdays = array(
            8   => $translator->trans("generic.monday"),
            16  => $translator->trans("generic.tuesday"),
            32  => $translator->trans("generic.wednesday"),
            64  => $translator->trans("generic.thursday"),
            128 => $translator->trans("generic.friday"),
            256 => $translator->trans("generic.saturday"),
            512 => $translator->trans("generic.sunday")
        );
        $transformer = new WeekdaysToArrayTransformer();

        $builder
            ->add(
                $builder->create(
                    'weekdays',
                    'choice',
                    array(
                        'choices' => $weekdays,
                        'multiple' => true,
                        'expanded' => true,
                        'label' => 'admin.timeblock.weekdays'
                    )
                )->addModelTransformer($transformer)
            )
            ->add('intervalBegin', 'date', array('widget' => 'single_text', 'empty_value' => '', 'label' => 'admin.timeblock.intervalBegin'))
            ->add('intervalEnd', 'date', array('widget' => 'single_text', 'empty_value' => '', 'label' => 'admin.timeblock.intervalEnd'))
            ->add('begin', 'time', array('label' => 'admin.timeblock.begin'))
            ->add('end', 'time', array('label' => 'admin.timeblock.end'))
            ->add('isActive', 'checkbox', array('label' => 'admin.timeblock.isActive'))
            ->add('title', 'text', array('label' => 'admin.timeblock.title'))
            ->add(
                'eventType',
                'entity',
                array(
                    'class' => 'OktolabRentBundle:EventType',
                    'property' => 'name',
                    'required' => true,
                    'label'     => 'admin.timeblock.eventType'
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oktolab\Bundle\RentBundle\Entity\Timeblock'
        ));
        $resolver->setRequired(array(
            'translator'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oktolab_bundle_rentbundle_timeblock';
    }
}
