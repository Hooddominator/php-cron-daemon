<?php

namespace CronDaemon\Console\Handler\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use CronDaemon\Console\Handler\RunDaemonConsoleHandler;

class RunDaemonConsoleHandlerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null) {

        $daemonService = $container->get(\CronDaemon\Service\DaemonService::class);

        return new RunDaemonConsoleHandler($daemonService);
    }

}
