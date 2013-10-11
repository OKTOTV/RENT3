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
        $builder->add('name')
            ->add('description', 'textarea', array('required' => false))
            ->add('begin', 'datetime', array('widget' => 'single_text', 'required' => true, 'empty_value' => ''))
            ->add('end', 'datetime', array('widget' => 'single_text', 'required' => true, 'empty_value' => ''))
            ->add('objects', 'collection', array('type' => new EventObjectType(), 'allow_add' => true));

        $builder->add('cancel', 'submit', array('validation_groups' => false))
            ->add('update', 'submit')
            ->add('delete', 'submit')
            ->add('rent', 'submit');
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Oktolab\Bundle\RentBundle\Entity\Event'));
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
