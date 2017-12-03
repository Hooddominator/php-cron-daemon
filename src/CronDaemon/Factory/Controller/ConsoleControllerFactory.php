<?php

namespace CronDaemon\Factory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CronDaemon\Controller\ConsoleController;

class ConsoleControllerFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $controllerService) {

        $realServiceLocator = $controllerService->getServiceLocator();

        $daemonService = $realServiceLocator->get("CronDaemon\DaemonService");

        $controller = new ConsoleController($daemonService);
        return $controller;
    }

}
