<?php
namespace Luxifer\Leboncoin\Command;

use Symfony\Component\Console\Command\Command;

class ContainerAwareCommand extends Command
{
    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
    }

    protected function getConfiguration()
    {
        return $this->getApplication()->getConfiguration();
    }
}
