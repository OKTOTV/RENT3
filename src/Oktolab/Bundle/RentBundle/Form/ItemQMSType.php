<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oktolab\Bundle\RentBundle\Form\QMSStatusType;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Qms;

/**
 * ItemQMSType contains all qms for an item to de/activate problems and quit it with a new state
 *
 * @author rs
 */
class ItemQMSType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'qms',
                new QMSType(array(Qms::STATE_OKAY, Qms::STATE_DISCARDED)),
                array('mapped' => false)
            )
            ->add(
                'qmss',
                'collection',
                array(
                    'type' => new QMSStatusType()
                ))
            ->add('save', 'submit', array('label' => 'qms.submit'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oktolab\Bundle\RentBundle\Entity\Inventory\Item',
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'orb_item_change_qms_form';
    }
}
