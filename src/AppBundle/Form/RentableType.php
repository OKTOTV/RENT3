<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RentableType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class,
                [
                    'label' => 'oktolab.rentable_title_label',
                    'attr'  => ['placeholder' => 'oktolab.rentable_title_placeholder']
                ]
            )
            ->add('description', TextareaType::class,
                [
                    'label' => 'oktolab.rentable_description_label',
                    'attr'  => ['placeholder' => 'oktolab.rentable_description_placeholder']
                ]
            )
            ->add('isActive', CheckboxType::class,
                [
                    'label' => 'oktolab.rentable_isActive_label'
                ]
            )
            ->add('barcode', TextType::class,
                [
                    'label' => 'oktolab.rentable_barcode_label',
                    'attr'  => ['placeholder' => 'oktolab.rentable_barcode_placeholder']
                ]
            )
            ->add('sets', CollectionType::class,
                [
                    'label' => 'oktolab.rentable_sets_label'
                ]
            )
            ->add('type', EntityType::class,
                [
                    'label' => 'oktolab.rentable_type_label',
                    'class' => 'AppBundle:Type'
                ])
            ->add('count', IntegerType::class,
                [
                    'label' => 'oktolab.rentable_count_label'
                ])
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Rentable'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oktolab_rentable';
    }
}
