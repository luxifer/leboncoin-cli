<?php
namespace Luxifer\Leboncoin\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fetch')
            ->setDescription('Fetch bids from Leboncoin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $config = $this->getConfiguration();

        foreach ($config['leboncoin']['criterias'] as $criteria) {
            $output->writeln(sprintf('<info>%s</info>', $criteria['title']));
            $bids = $container['client']->fetch($criteria);
            $output->writeln($container['client']->getRequestUrl());

            foreach($bids as $bid) {
                $prefix = '';

                if ($bid['isPro']) {
                    $prefix = '<bg=yellow;fg=black>[pro]</bg=yellow;fg=black> ';
                }

                $output->writeln('  - '.$prefix.$bid['title'].' - '.sprintf('<fg=red>%s</fg=red>', $bid['price']).' - '.$bid['created_at']->format('d/m/Y H:i'));
                $output->writeln('    Id: '.$bid['bid_id']);
                $output->writeln('    Url: '.$bid['url']);
                $output->writeln('    Picture: '.$bid['picture']);
                $output->writeln('    Placement: '.$bid['placement']);
                $output->writeln('');
            }
        }
    }
}
