<?php

namespace CronDaemonTest\Service;

use CronDaemonTest\Bootstrap;
use PHPUnit\Framework\TestCase;
use Interop\Container\ContainerInterface;

class DaemonServiceTest extends TestCase {
    public function testServiceManager() {
        $container = Bootstrap::getServiceManager();
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }
}
