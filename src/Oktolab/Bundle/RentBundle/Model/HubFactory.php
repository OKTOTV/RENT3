<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class HubFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
         $providerid = 'oktolab.hub_authentication_provider';
         $listenerid = 'oktolab.hub_security_listener';

         return array($providerid, $listenerid, $defaultEntryPoint);
    }

    public function getKey()
    {
         return 'oktolab';
    }

    public function getPosition()
    {
         return 'pre_auth';
    }

    public function addConfiguration(NodeDefinition $builder)
    {
         ;
    }
}
