<?php
namespace Luxifer\Leboncoin\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('setup')
            ->setDescription('Setup database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $sm = $container['db']->getSchemaManager();
        $fromSchema = $sm->createSchema();
        $toSchema = clone $fromSchema;

        if ($toSchema->hasTable('bid')) {
            $toSchema->dropTable('bid');
        }

        $bidTable = $toSchema->createTable('bid');
        $bidTable->addColumn('id', 'integer', array('autoincrement' => true));
        $bidTable->addColumn('bid_id', 'string');
        $bidTable->addColumn('price', 'string', array('notnull' => false));
        $bidTable->addColumn('title', 'string');
        $bidTable->addColumn('placement', 'string');
        $bidTable->addColumn('url', 'string', array('length' => 2000));
        $bidTable->addColumn('picture', 'string', array('length' => 2000, 'notnull' => false));
        $bidTable->addColumn('is_pro', 'boolean', array('default' => false));
        $bidTable->addColumn('created_at', 'datetime');
        $bidTable->addColumn('inserted_at', 'datetime', array('default' => 'CURRENT_TIMESTAMP'));

        $sql = $fromSchema->getMigrateToSql($toSchema, $container['db']->getDatabasePlatform());

        $output->writeln('<info>Updating schema</info>');

        foreach ($sql as $query) {
            $output->writeln(sprintf('<comment>%s</comment>', $query));
            $container['db']->exec($query);
        }
    }
}
