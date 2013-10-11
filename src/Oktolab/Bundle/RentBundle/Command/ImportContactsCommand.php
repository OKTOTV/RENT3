<?php

namespace Oktolab\Bundle\RentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Oktolab\Bundle\RentBundle\Model\ContactProvider;

class ImportContactsCommand extends ContainerAwareCommand
{
     protected function configure()
     {
         $this->setName('rentBundle:importContacts')
                ->addOption('dryrun', 'd', InputOption::VALUE_NONE, 'Makes a Dryrun and gives you a number of contacts that WOULD be imported')
                ->addArgument('name', InputOption::VALUE_REQUIRED, 'The string to search for in contactsapi');
     }

     protected function execute(InputInterface $input, OutputInterface $output)
     {
         $contactProvider = $this->getContainer()->get('oktolab.contact_provider');

         if ($input->getOption('verbose')) {
             $output->writeln('Start getting Contacts from FLOW');
         }

         $hubContacts = $contactProvider->getContactsByName($input->getArgument('name'));

         if ($input->getOption('verbose')) {
             $output->writeln(sprintf('Found %s Contacts in FLOW', count($hubContacts)));
         }

         $localContacts = $contactProvider->getContactsByName('*', ContactProvider::$Resource_RENT);

         if ($input->getOption('verbose')) {
             $output->writeln(sprintf('Found %s Contacts in RENT (local)', count($localContacts)));
             $output->writeln('Start search for new ones');
         }

         $newContacts = $this->getNewContacts($hubContacts, $localContacts);

         if ($input->getOption('verbose')) {
             $output->writeln(sprintf('Found %s Contacts to import to RENT!', count($newContacts)));
         }

         if (!$input->getOption('dryrun')) {
            $output->writeln('Start import to Database. This could take a while');
            $contactProvider->addContactsToRent($newContacts);
            $output->writeln('Import successful!');
         }

         if ($input->getOption('verbose') && !$input->getOption('dryrun')) {
             $output->writeln(sprintf('%s Contacts now in RENT', count($contactProvider->getContactsByName('*', ContactProvider::$Resource_RENT))));
         }
     }

     private function getNewContacts($hubContacts, $localContacts)
     {
         $contacts = $hubContacts;

         if ($localContacts) {
             foreach($hubContacts as $key=>$hubContact) {

                 foreach($localContacts as $localCostUnit) {

                     if ($hubContact->getGuid() == $localCostUnit->getGuid()) {
                         unset($contacts[$key]);
                     }
                 }
             }
         }
         return $contacts;
     }
}
