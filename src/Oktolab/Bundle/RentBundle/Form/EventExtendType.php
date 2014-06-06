<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Oktolab\Bundle\RentBundle\Form\EventObjectType;

/**
 * The Event Defer Type. Its only possible to change the return date.
 */
class EventExtendType extends AbstractType
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
            ->add(
                'end',
                'datetime',
                array(
                    'widget'        => 'single_text',
                    'required'      => true,
                    'label'         => 'event.end'
                )
            );

        $builder->add('extend', 'submit');
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
                'error_bubbling'        => false,
                'validation_groups'     => array('Event', 'Logic'),
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'orb_event_defer_form';
    }
}
