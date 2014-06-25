<?php

namespace Oktolab\Bundle\RentBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role', 'choice', array(
               'choices' => array('ROLE_USER' => 'Benutzer', 'ROLE_ADMIN' => 'Admin'),
               'label' => 'admin.user.role'
                )
            )
            ->add('displayname', 'text', array('label' => 'admin.user.displayname'))
            ->add('username', 'text', array('label' => 'admin.user.short'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    public function getName()
    {
        return 'oktolabrentbundle_user';
    }
}
