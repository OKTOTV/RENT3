<?php

namespace Oktolab\Bundle\RentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oktolab\Bundle\RentBundle\Entity\Inventory\QMS;

/**
 * CheckType Form for events in check (QMS)
 *
 * @author rs
 */
class QMSType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $states = array(
            QMS::STATE_OKAY => 'qms.okay',
            QMS::STATE_FLAW => 'qms.flaw',
            QMS::STATE_DAMAGED => 'qms.damaged',
            QMS::STATE_DESTROYED => 'qms.destroyed',
            QMS::STATE_STOLEN => 'qms.stolen'
        );

        $builder
            ->add('status', 'choice', array('choices' => $states, 'label' => 'qms.status'))
            ->add('description', 'textarea', array('label' => 'qms.description'))
            ->add(
                'item',
                'entity',
                array(
                    'class' => 'OktolabRentBundle:Inventory\Item',
                    'property' => 'id',
                    'required' => true)
                )
            ->add(
                'event',
                'entity',
                array(
                    'class' => 'OktolabRentBundle:Event',
                    'property' => 'id',
                    'required' => true)
                );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oktolab\Bundle\RentBundle\Entity\Inventory\Qms',
        ));
    }

    public function getName()
    {
        return 'ORB_Event_Check_Form';
    }
}
