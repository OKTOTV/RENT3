<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oktolab\Bundle\RentBundle\Form\QMSType;
/**
 * EventQMSType contains all QMS blogs of QMSTYPE
 *
 * @author rs
 */
class EventQMSType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'qmss',
                'collection',
                array(
                    'type' => new QMSType()
                )
            )
            ->add('save', 'submit', array('label' => 'qms.submit'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oktolab\Bundle\RentBundle\Entity\Event',
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'ORB_Event_QMS_Check_Form';
    }
}
