<?php
namespace Luxifer\Leboncoin\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class NotifyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('notify')
            ->setDescription('Send bids from Leboncoin by mail')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $config = $this->getConfiguration();

        $command = $this->getApplication()->find('fetch');

        $arguments = array(
            'command' => 'fetch',
        );

        $newInput = new ArrayInput($arguments);
        $returnCode = $command->run($newInput, new NullOutput());
        $output->writeln('Sending mails for alert:');

        foreach ($config['leboncoin']['criterias'] as $criteria) {
            $alert = $container['alert.manager']->find($criteria);
            $bids = $container['alert.manager']->fetchBidsToSend($alert['id']);

            if (!count($bids)) {
                continue;
            }

            $output->writeln(sprintf(' - <info>%s</info>', $alert['title']));
            $output->writeln('');

            $ids = array_column($bids, 'id');

            $bodyHtml = $container['twig']->render('alert.html.twig', array(
                'title' => $alert['title'],
                'url'   => $alert['url'],
                'bids'  => $bids
            ));

            $bodyText = $container['twig']->render('alert.txt.twig', array(
                'title' => $alert['title'],
                'url'   => $alert['url'],
                'bids'  => $bids
            ));

            $message = \Swift_Message::newInstance()
                ->setSubject('Notification from '.sprintf('%s %s', $this->getApplication()->getName(), $this->getApplication()->getVersion()))
                ->setFrom($config['leboncoin']['from_email'])
                ->setTo($config['leboncoin']['to_email'])
                ->setBody($bodyHtml, 'text/html')
                ->addPart($bodyText, 'text/plain')
            ;

            $result = $container['mailer']->send($message);

            if ($result) {
                $container['bid.manager']->sent($ids);
            }
        }
    }
}
