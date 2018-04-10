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

namespace CronDaemon\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Hydrator\ClassMethods as ClassMethodsHydrator;
use CronDaemon\Model\DaemonTaskModel;

class ModuleOptions extends AbstractOptions {

    protected $tasks;

    public function __construct($options = null) {
        $this->tasks = new \ArrayIterator();
        $this->__strictMode__ = false;
        parent::__construct($options);
    }

    public function getTasks() {
        return $this->tasks;
    }

    protected function setTasks($tasks) {
        foreach ($tasks as $task_config) {
            $task = $this->createTask($task_config);
            if ($task->isValid()) {

                $this->tasks->append($task);
            }
        }
    }

    protected function createTask($task_config) {
        $task = new DaemonTaskModel();
        $hydrator = new ClassMethodsHydrator();

        if (!is_array($task_config)) {
            return $task;
        }

        $task = $hydrator->hydrate($task_config, $task);
        return $task;
    }

}
