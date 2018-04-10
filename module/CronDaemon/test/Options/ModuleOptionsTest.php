<?php

namespace CronDaemonTest\Options;

use CronDaemonTest\Bootstrap;
use PHPUnit\Framework\TestCase;
use Interop\Container\ContainerInterface;
use CronDaemon\Options\ModuleOptions;

class ModuleOptionsTest extends TestCase {
    public function testModuleOptions() {
        $container = Bootstrap::getServiceManager();

        $module_config = $container->get('config')['cron_daemon'];
        $options = new ModuleOptions($module_config);

        $this->assertInstanceOf(ModuleOptions::class, $options);
    }
}

