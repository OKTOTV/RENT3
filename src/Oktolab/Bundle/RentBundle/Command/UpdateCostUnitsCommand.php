<?php

namespace Oktolab\Bundle\RentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Oktolab\Bundle\RentBundle\Model\CostUnitProvider;

class UpdateCostUnitsCommand extends ContainerAwareCommand
{
     protected function configure()
     {
         $this->setName('rentBundle:updateCostUnits')
                ->addOption('dryrun', 'd', InputOption::VALUE_NONE, 'Makes a Dryrun and gives you a number of costunits that WOULD be imported');
     }

     protected function execute(InputInterface $input, OutputInterface $output)
     {
         $CostUnitProvider = $this->getContainer()->get('oktolab.cost_unit_provider');

         if ($input->getOption('verbose')) {
             $output->writeln('Start getting CostUnits from FLOW');
         }

         $flowCostUnits = $CostUnitProvider->getCostUnitsFromResource();

         if ($input->getOption('verbose')) {
             $output->writeln(sprintf('Found %s CostUnits in FLOW', count($flowCostUnits)));
         }

         $localCostUnits = $CostUnitProvider->getCostUnitsFromResource(CostUnitProvider::$Resource_RENT);

         if ($input->getOption('verbose')) {
             $output->writeln(sprintf('Found %s CostUnits in RENT (local)', count($localCostUnits)));
             $output->writeln('Start search for new ones');
         }

         $newCostUnits = $this->getNewCostUnits($flowCostUnits, $localCostUnits);

         if ($input->getOption('verbose')) {
             $output->writeln(sprintf('Found %s CostUnits to import to RENT!', count($newCostUnits)));
         }

         if (!$input->getOption('dryrun')) {
            if ($input->getOption('verbose')) {
                $output->writeln('Start import to Database. This could take a while');
            }

            $CostUnitProvider->addCostUnitsToRent($newCostUnits);

            if ($input->getOption('verbose')) {
                $output->writeln('Import successful!');
            }
         }

         if ($input->getOption('verbose') && !$input->getOption('dryrun')) {
             $output->writeln(sprintf('%s CostUnits now in RENT', count($CostUnitProvider->getCostUnitsFromResource(CostUnitProvider::$Resource_RENT))));
         }
     }

     private function getNewCostUnits($flowCostUnits, $localCostUnits)
     {
         $costUnits = $flowCostUnits;

         if ($localCostUnits) {
             foreach($flowCostUnits as $key=>$flowCostUnit) {
                 foreach($localCostUnits as $localCostUnit) {

                     if ($flowCostUnit->getGuid() == $localCostUnit->getGuid()) {
                         unset($costUnits[$key]);
                     }
                 }
             }
         }
         return $costUnits;
     }
}
