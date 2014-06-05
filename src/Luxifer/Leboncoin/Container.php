<?php
namespace Luxifer\Leboncoin;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;
use Luxifer\Leboncoin\Configuration\DatabaseConfiguration;
use Luxifer\Leboncoin\Configuration\MailerConfiguration;
use Luxifer\Leboncoin\Configuration\LeboncoinConfiguration;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Luxifer\Leboncoin\Http\Client;
use Luxifer\Leboncoin\Manager\BidManager;
use Luxifer\Leboncoin\Manager\AlertManager;

class Container extends \Pimple
{
    public function __construct()
    {
        parent::__construct();

        $this->registerConfiguration();
        $this->setupDatabase();
        $this->setupClient();
        $this->setupManager();
        $this->setupTwig();
        $this->setupMailer();
    }

    protected function registerConfiguration()
    {
        $database = Yaml::parse(__DIR__.'/../../../config/database.yml');
        $mailer = Yaml::parse(__DIR__.'/../../../config/mailer.yml');
        $leboncoin = Yaml::parse(__DIR__.'/../../../config/leboncoin.yml');
        $processor = new Processor();
        $configuration = array();

        $configuration['database'] = $processor->processConfiguration(
            new DatabaseConfiguration(),
            array($database)
        );

        $configuration['mailer'] = $processor->processConfiguration(
            new MailerConfiguration(),
            array($mailer)
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

    protected function setupManager()
    {
        $this['bid.manager'] = function ($container) {
            return new BidManager($container['db']);
        };
        $this['alert.manager'] = function ($container) {
            return new AlertManager($container['db']);
        };
    }

    protected function setupTwig()
    {
        $this['twig'] = function ($container) {
            $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../../templates');

            return new \Twig_Environment($loader);
        };
    }

    protected function setupMailer()
    {
        $config = $this['configuration']['mailer']['swift'];

        $this['mailer'] = function ($container) use ($config) {
            if ($config['type'] == 'smtp') {
                $transport = \Swift_SmtpTransport::newInstance($config['host'], $config['host'], $config['security']);

                if (null !== $config['user']) {
                    $transport->setUsername($config['user']);
                }

                if (null !== $config['password']) {
                    $transport->setPassword($config['password']);
                }

                if (null !== $config['auth_mode']) {
                    $transport->setAuthMode($config['auth_mode']);
                }
            } elseif ($config['type'] == 'sendmail') {
                $transport = \Swift_SendmailTransport::newInstance($config['path']);
            } else {
                $transport = \Swift_MailTransport::newInstance();
            }

            return \Swift_Mailer::newInstance($transport);
        };
    }
}
