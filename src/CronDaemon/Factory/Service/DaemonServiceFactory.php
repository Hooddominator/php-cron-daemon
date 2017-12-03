<?php

namespace CronDaemon\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CronDaemon\Service\DaemonService;

class DaemonServiceFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $service) {

        $options = $service->get('CronDaemon\ModuleOptions');
        $service = new DaemonService($options);

        return $service;
    }

}
