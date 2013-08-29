<?php

namespace Oktolab\Bundle\RentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Oktolab\Bundle\RentBundle\Model\HubFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OktolabRentBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new HubFactory());
    }
}
