<?php

/*
 * The MIT License
 *
 * Copyright 2017 wiryonolau.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace CronDaemon\Service;

use CronDaemon\Options\ModuleOptions;

class DaemonService {

    protected $options;
    protected $running = true;

    public function __construct(ModuleOptions $options) {
        $this->options = $options;
    }

    public function runDaemon() {
        declare(ticks = 1);

        pcntl_signal(SIGTERM, [$this, "signalHandler"]);
        pcntl_signal(SIGHUP, [$this, "signalHandler"]);
        pcntl_signal(SIGINT, [$this, "signalHandler"]);
        pcntl_signal(SIGUSR1, [$this, "signalHandler"]);

        while ($this->running) {
            $this->runTask();

            $this->killTimeout();

            $this->killDefunc();

            time_nanosleep(0, 500000000);
        }
    }

    protected function signalHandler($signal) {
        $this->running = false;
        
        foreach ($this->options->getTasks() as $task) {
            $task->killAll();
        }
    }

    protected function runTask() {
        foreach ($this->options->getTasks() as $task) {
            $task->run();
        }
    }

    protected function killTimeout() {
        foreach ($this->options->getTasks() as $task) {
            $task->killTimeout();
        }
    }

    protected function killDefunc() {
        foreach ($this->options->getTasks() as $task) {
            $dead_and_gone = pcntl_waitpid(-1, $status, WNOHANG);
            while ($dead_and_gone > 0) {
                $task->killPid($dead_and_gone);

                $dead_and_gone = pcntl_waitpid(-1, $status, WNOHANG);
            }
        }
    }

}
