<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Oktolab\Bundle\RentBundle\Form\EventObjectType;

/**
 * The Event Type.
 */
class EventType extends AbstractType
{

    /**
     * Build Form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add(
                'contact',
                'entity',
                array(
                    'class'     => 'OktolabRentBundle:Contact',
                    'property' => 'id',
                    'required' => true,
                    'empty_value' => 'Choose a contact',
                    'attr'      => array('style' => 'display:none'),
                    'label_attr' => array('style' => 'display:none')
                )
            )
            ->add(
                'costunit',
                'entity',
                array(
                    'class'     => 'OktolabRentBundle:CostUnit',
                    'property' => 'id',
                    'required' => true,
                    'attr'      => array('style' => 'display:none'),
                    'label_attr' => array('style' => 'display:none')
                )
            )
            ->add('description', 'textarea', array('required' => false))
            ->add('begin', 'datetime', array('widget' => 'single_text', 'required' => true))
            ->add('end', 'datetime', array('widget' => 'single_text', 'required' => true))
            ->add('objects', 'collection', array('type' => new EventObjectType(), 'allow_add' => true, 'allow_delete' => true));

        $builder->add('cancel', 'submit', array('validation_groups' => false))
            ->add('update', 'submit')
            ->add('delete', 'submit')
            ->add('rent', 'submit', array('validation_groups' => array('Event', 'Logic', 'Rent')));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'            => 'Oktolab\Bundle\RentBundle\Entity\Event',
                'cascade_validation'    => true,
                'validation_groups'     => array('Event', 'Logic'),
            )
        );

        $resolver->setRequired(array('em'));
        $resolver->setAllowedTypes(array('em' => 'Doctrine\ORM\EntityManager'));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'OktolabRentBundle_Event_Form';
    }
}
