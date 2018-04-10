<?php

namespace CronDaemon;

return array(
    'service_manager' => array(
        'factories' => array(
            Console\Handler\RunDaemonConsoleHandler::class => Console\Handler\Factory\RunDaemonConsoleHandlerFactory::class,
            Service\DaemonService::class => Service\Factory\DaemonServiceFactory::class,
            Options\ModuleOptions::class => Options\Factory\ModuleOptionsFactory::class
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                array(
                    'name' => 'daemon',
                    'route' => 'run',
                    'short_description' => 'Run Daemon Process',
                    'handler' => Console\Handler\RunDaemonConsoleHandler::class
                ),
            ),
        ),
    ),
);
