<?php
namespace Oktolab\Bundle\RentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony;

class LogListener {

    private $logger;

    public function __construct(Symfony\Bridge\Monolog\Logger $logger = null)
    {
        $this->logger = $logger;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        if ($this->logger) {
            $entity = $args->getEntity();

            switch (\get_class($entity)) {
             case 'Oktolab\Bundle\RentBundle\Entity\Inventory\Item':
                 $this->logger->debug(sprintf('Try to persist a new item. (%s)', $entity->getTitle()));
                 break;
             case 'Oktolab\Bundle\RentBundle\Entity\Inventory\Set':
                 $this->logger->debug(sprintf('Try to persist a new set. (%s)', $entity->getTitle()));
                 break;

             default:
                 $this->logger->debug('Unknown entity will get persisted!');
                 break;
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        switch (\get_class($entity)) {
         case 'Oktolab\Bundle\RentBundle\Entity\Inventory\Item':
             $this->logger->info(sprintf('New item persisted. (%s)', $entity->getTitle()));
             break;
         case 'Oktolab\Bundle\RentBundle\Entity\Inventory\Set':
             $this->logger->info(sprintf('New set persisted. (%s)', $entity->getTitle()));
             break;

         default:
             $this->logger->info('Unknown entity persisted!');
             break;
        }
    }

}