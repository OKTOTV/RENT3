<?php
namespace Oktolab\Bundle\RentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
//use Oktolab\RentBundle\Entity\Inventory\Item;
//use Symfony\Bridge\Monolog\Logger;
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
             case 'Item':
                 $this->logger->debug(sprintf('Try to persist a new item. (%s)', $entity->getTitle()));
                 break;
             case 'Set':
                 $this->logger->debug(sprintf('Try to persist a new set. (%s)', $entity->getTitle()));
                 break;

             default:
                 $this->logger->debug('Unknown entity will get persisted!');
                 break;
            }
        }
    }

    public function postPersist(LifecycleEnventArgs $args)
    {
        $entity = $args->getEntity();

        switch (\get_class($entity)) {
         case 'Item':
             $this->get('logger')->debug(sprintf('New item persisted. (%s)', $entity->getTitle()));
             break;
         case 'Set':
             $this->get('logger')->debug(sprintf('New set persisted. (%s)', $entity->getTitle()));
             break;

         default:
             $this->get('logger')->debug('Unknown entity persisted!');
             break;
        }
    }

}