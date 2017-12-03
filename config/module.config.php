<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'CronDaemon\DaemonService' => 'CronDaemon\Factory\Service\DaemonServiceFactory',
            'CronDaemon\ModuleOptions' => 'CronDaemon\Factory\Options\ModuleOptionsFactory'
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'CronDaemon\Controller\Console' => 'CronDaemon\Factory\Controller\ConsoleControllerFactory',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'daemon-run' => array(
                    'options' => array(
                        'route' => 'daemon run',
                        'defaults' => array(
                            'controller' => 'CronDaemon\Controller\Console',
                            'action' => 'run',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
