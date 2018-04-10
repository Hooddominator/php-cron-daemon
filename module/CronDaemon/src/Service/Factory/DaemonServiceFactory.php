<?php

namespace CronDaemon\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use CronDaemon\Service\DaemonService;

class DaemonServiceFactory implements FactoryInterface {

     public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null) {

        $options = $container->get(\CronDaemon\Options\ModuleOptions::class);

        return new DaemonService($options);
    }

}
