<?php

namespace Oktolab\Bundle\RentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Oktolab\Bundle\RentBundle\Model\CostUnitProvider;

/**
 * Command to update CostUnits from Oktolab FLOW.
 */
class UpdateCostUnitsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('rent:update-costunits')
               ->setDescription('Updates CostUnits from Oktolab FLOW.')
               ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Makes a dry run.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $costUnitProvider = $this->getContainer()->get('oktolab.cost_unit_provider');
        $flowCostUnits = $costUnitProvider->getCostUnitsFromResource();
        $localCostUnits = $costUnitProvider->getCostUnitsFromResource(CostUnitProvider::$Resource_RENT);
        $newCostUnits = $this->getNewCostUnits($flowCostUnits, $localCostUnits);

        if ($input->getOption('verbose')) {
            $output->writeln(sprintf('Found %d CostUnits in Oktolab FLOW.', count($flowCostUnits)));
            $output->writeln(sprintf('Found %d CostUnits in Oktolab RENT.', count($localCostUnits)));
            $output->writeln(sprintf('Will update %d CostUnits in Oktolab RENT.', count($newCostUnits)));
        }

        if (!$input->getOption('dry-run')) {
            $output->writeln('Updating database. This could take a while ...');
            $costUnitProvider->addCostUnitsToRent($newCostUnits);

            // @TODO: Check if the update was *really* successful (e.g. return true/false ...)
            $output->writeln('Update successful!');
        }
    }

    /**
     * Returns delta of new CostUnits.
     *
     * @param array $flowCostUnits
     * @param array $localCostUnits
     *
     * @return array
     */
    private function getNewCostUnits($flowCostUnits, $localCostUnits)
    {
        if (!$localCostUnits) {
            return $flowCostUnits;
        }

        $costUnits = $flowCostUnits;
        foreach ($flowCostUnits as $key => $flowCostUnit) {
            foreach ($localCostUnits as $localCostUnit) {
                if ($flowCostUnit->getGuid() == $localCostUnit->getGuid()) {
                    unset($costUnits[$key]);
                }
            }
        }

        return $costUnits;
    }
}
