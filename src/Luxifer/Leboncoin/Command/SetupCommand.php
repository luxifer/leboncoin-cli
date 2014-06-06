<?php
namespace Luxifer\Leboncoin\Command;

use Symfony\Component\Console\Input\InputInterface;
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

        if ($toSchema->hasTable('alert')) {
            $toSchema->dropTable('alert');
        }

        if ($toSchema->hasTable('alert_bid')) {
            $toSchema->dropTable('alert_bid');
        }

        $alertTable = $toSchema->createTable('alert');
        $alertTable->addColumn('id', 'integer', array('autoincrement' => true));
        $alertTable->addColumn('key', 'string');
        $alertTable->addColumn('config_hash', 'string', array('length' => 32));
        $alertTable->addColumn('title', 'string');
        $alertTable->addColumn('url', 'string', array('length' => 2000));
        $alertTable->addColumn('created_at', 'datetime', array('default' => 'CURRENT_TIMESTAMP'));
        $alertTable->setPrimaryKey(array('id'));
        $alertTable->addUniqueIndex(array('config_hash'));

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
        $bidTable->addColumn('is_sent', 'boolean', array('default' => false));
        $bidTable->setPrimaryKey(array('id'));
        $bidTable->addUniqueIndex(array('bid_id'));
        $bidTable->addUniqueIndex(array('url'));
        $bidTable->addUniqueIndex(array('picture'));

        $alertBidTable = $toSchema->createTable('alert_bid');
        $alertBidTable->addColumn('alert_id', 'integer');
        $alertBidTable->addColumn('bid_id', 'integer');
        $alertBidTable->addForeignKeyConstraint($alertTable, array('alert_id'), array('id'));
        $alertBidTable->addForeignKeyConstraint($bidTable, array('bid_id'), array('id'));
        $alertBidTable->addUniqueIndex(array('alert_id', 'bid_id'));

        $sql = $fromSchema->getMigrateToSql($toSchema, $container['db']->getDatabasePlatform());

        $output->writeln('<info>Updating schema</info>');

        foreach ($sql as $query) {
            $output->writeln(sprintf('<comment>%s</comment>', $query));
            $container['db']->exec($query);
        }
    }
}
