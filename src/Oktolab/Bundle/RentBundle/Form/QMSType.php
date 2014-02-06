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
    private $states;

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', 'choice', array('choices' => $this->states, 'label' => 'qms.status'))
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
            'error_bubbling' => false
        ));
    }

    public function getName()
    {
        return 'ORB_Event_Check_Form';
    }

    public function __construct(array $states = null)
    {
        $statePossibilitys = array(
            QMS::STATE_OKAY         => 'qms.okay',
            QMS::STATE_FLAW         => 'qms.flaw',
            QMS::STATE_DAMAGED      => 'qms.damaged',
            QMS::STATE_DESTROYED    => 'qms.destroyed',
            QMS::STATE_LOST         => 'qms.lost',
            QMS::STATE_MAINTENANCE  => 'qms.maintenance',
            QMS::STATE_DISCARDED    => 'qms.discarded'
        );

        $statesToUse = array();
        if (null == $states) {
            $this->states = $statePossibilitys;
        } else {
            foreach ($states as $key => $value) {
                $statesToUse[$value] = $statePossibilitys[$value];
            }
        }
        $this->states = $statesToUse;
    }
}
