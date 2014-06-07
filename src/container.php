<?php

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;
use Luxifer\Leboncoin\Configuration\DatabaseConfiguration;
use Luxifer\Leboncoin\Configuration\MailerConfiguration;
use Luxifer\Leboncoin\Configuration\LeboncoinConfiguration;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Luxifer\Leboncoin\Manager\BidManager;
use Luxifer\Leboncoin\Manager\AlertManager;
use Guzzle\Http\Client as GuzzleClient;
use Luxifer\Leboncoin\Http\Client as LeboncoinClient;

$app = new \Pimple();

$app['configuration'] = function ($app) {
    $database = Yaml::parse(__DIR__.'/../config/database.yml');
    $mailer = Yaml::parse(__DIR__.'/../config/mailer.yml');
    $leboncoin = Yaml::parse(__DIR__.'/../config/leboncoin.yml');
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

    return $configuration;
};

$app['db'] = function($app) {
    return DriverManager::getConnection($app['configuration']['database']['connection'], new Configuration());
};

$app['guzzle'] = function ($app) {
    $guzzle = new GuzzleClient($app['configuration']['leboncoin']['url']);

    if (null !== $proxy = $app['configuration']['leboncoin']['proxy']) {
        $guzzle->setConfig(array(
            'proxy' => $proxy
        ));
    }

    return $guzzle;
};

$app['client'] = function($app) {
    return new LeboncoinClient($app['guzzle']);
};

$app['bid.manager'] = function ($app) {
    return new BidManager($app['db']);
};

$app['alert.manager'] = function ($app) {
    return new AlertManager($app['db']);
};

$app['twig'] = function ($app) {
    $loader = new \Twig_Loader_Filesystem(__DIR__.'/../templates');

    return new \Twig_Environment($loader);
};

$app['mailer'] = function ($app) {
    $config = $app['configuration']['mailer']['swift'];

    if ($config['type'] == 'smtp') {
        $transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'], $config['security']);

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

return $app;
