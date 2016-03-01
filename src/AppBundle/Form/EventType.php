<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startAt', DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'html5' => false,
                    'label' => 'oktolab.event_startAt_label',
                    'attr' => ['placeholder' => 'oktolab.event_startAt_placeholder']
                ]
            )
            ->add('endAt', DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'html5' => false,
                    'label' => 'oktolab.event_startAt_label',
                    'attr' => ['placeholder' => 'oktolab.event_endAt_placeholder']
                ]
            )
            ->add('description', TextareaType::class,
                [
                    'label' => 'oktolab.event_description_label',
                    'attr'  => ['placeholder' => 'oktolab.event_description_placeholder']
                ])
            ->add('rentables', CollectionType::class,
                [
                    'label' => 'oktolab.event_rentables_label'
                ])
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Event'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oktolab_event';
    }
}
