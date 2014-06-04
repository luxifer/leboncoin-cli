<?php
namespace Luxifer\Leboncoin;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Luxifer\Leboncoin\Command\FetchCommand;
use Luxifer\Leboncoin\Command\SetupCommand;

class Application extends BaseApplication
{
    protected $container;

    public function __construct()
    {
        $this->container = new Container();

        parent::__construct('Leboncoin CLI', file_get_contents(__DIR__.'/../../../VERSION'));
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new FetchCommand();
        $defaultCommands[] = new SetupCommand();

        return $defaultCommands;
    }

    public function getConfiguration()
    {
        return $this->container['configuration'];
    }

    public function getContainer()
    {
        return $this->container;
    }
}
