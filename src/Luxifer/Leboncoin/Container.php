<?php
namespace Luxifer\Leboncoin;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;
use Luxifer\Leboncoin\Configuration\DatabaseConfiguration;
use Luxifer\Leboncoin\Configuration\LeboncoinConfiguration;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Luxifer\Leboncoin\Http\Client;

class Container extends \Pimple
{
    public function __construct()
    {
        parent::__construct();
        $this->registerConfiguration();
        $this->setupDatabase();
        $this->setupClient();
    }

    protected function registerConfiguration()
    {
        $database = Yaml::parse(__DIR__.'/../../../config/database.yml');
        $leboncoin = Yaml::parse(__DIR__.'/../../../config/leboncoin.yml');
        $processor = new Processor();
        $configuration = array();

        $configuration['database'] = $processor->processConfiguration(
            new DatabaseConfiguration(),
            array($database)
        );

        $configuration['leboncoin'] = $processor->processConfiguration(
            new LeboncoinConfiguration(),
            array($leboncoin)
        );

        $this['configuration'] = $configuration;
    }

    protected function setupDatabase()
    {
        $this['db'] = function($container) {
            return DriverManager::getConnection($container['configuration']['database']['connection'], new Configuration());
        };
    }

    protected function setupClient()
    {
        $this['client'] = function($container) {
            return new Client($container['configuration']['leboncoin']['url']);
        };
    }
}
