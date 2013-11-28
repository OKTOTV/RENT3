<?php

namespace Oktolab\Bundle\RentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Oktolab\Bundle\RentBundle\Model\ContactProvider;

/**
 * Command to import contacts from Oktolab HUB.
 */
class ImportContactsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('rent:import-contacts')
               ->setDescription('Imports contacts from Oktolab HUB.')
               ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Makes a dry run.')
               ->addArgument('name', InputOption::VALUE_REQUIRED, 'The name to search for in contacts API.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $contactProvider = $this->getContainer()->get('oktolab.contact_provider');
        $hubContacts = $contactProvider->getContactsByName($input->getArgument('name'));
        $localContacts = $contactProvider->getContactsByName('*', ContactProvider::$Resource_RENT);
        $newContacts = $this->getNewContacts($hubContacts, $localContacts);

        if ($input->getOption('verbose')) {
            $output->writeln(sprintf('Found %d contacts in Oktolab HUB.', count($hubContacts)));
            $output->writeln(sprintf('Found %d contacts in Oktolab RENT.', count($localContacts)));
            $output->writeln(sprintf('Will import %d contacts to Oktolab RENT.', count($newContacts)));
        }

        if (!$input->getOption('dry-run')) {
            $output->writeln('Importing data. This could take a while ...');
            $contactProvider->addContactsToRent($newContacts);

            // @TODO: Check if the import was *really* successful (e.g. return true/false ...)
            $output->writeln('Import successful!');
        }
    }

    /**
     * Returns delta of new Contacts.
     *
     * @param array $hubContacts
     * @param array $localContacts
     *
     * @return array
     */
    private function getNewContacts($hubContacts, $localContacts)
    {
        if (!$localContacts) {
            return $hubContacts;
        }

        $contacts = $hubContacts;
        foreach ($hubContacts as $key => $hubContact) {
            foreach ($localContacts as $localContact) {
                if ($hubContact->getGuid() == $localContact->getGuid()) {
                    unset($contacts[$key]);
                }
            }
        }

        return $contacts;
    }
}
