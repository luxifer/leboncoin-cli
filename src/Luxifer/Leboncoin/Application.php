<?php
namespace Luxifer\Leboncoin;

use Symfony\Component\Console\Application as BaseApplication;
use Luxifer\Leboncoin\Command\FetchCommand;
use Luxifer\Leboncoin\Command\SetupCommand;
use Luxifer\Leboncoin\Command\NotifyCommand;

class Application extends BaseApplication
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;

        parent::__construct('Leboncoin CLI', file_get_contents(__DIR__.'/../../../VERSION'));
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new FetchCommand();
        $defaultCommands[] = new SetupCommand();
        $defaultCommands[] = new NotifyCommand();

        return $defaultCommands;
    }

    /**
     * Shortcut to get the configuration
     *
     * @return array processed configuration
     */
    public function getConfiguration()
    {
        return $this->container['configuration'];
    }

    /**
     * Return the Pimple container
     *
     * @return \Pimple container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
