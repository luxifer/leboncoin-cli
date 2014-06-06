<?php
namespace Luxifer\Leboncoin\Command;

use Symfony\Component\Console\Command\Command;

class ContainerAwareCommand extends Command
{
    /**
     * Shortcut to get the container
     *
     * @return \Pimple contaiber
     */
    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
    }

    /**
     * Shortcut to get the configuration
     *
     * @return array processed configuration
     */
    protected function getConfiguration()
    {
        return $this->getApplication()->getConfiguration();
    }
}
