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

namespace CronDaemon\Model;

use CronDaemon\Model\DaemonPidModel;
use Cron\CronExpression;

class DaemonTaskModel {

    const WORK_COUNT_MIN = 1;
    const WORK_COUNT_MAX = 10;

    protected $program = "/usr/bin/php";
    protected $arguments = array();
    protected $work_count = 1;
    protected $schedule = false;
    protected $timeout = 0;
    protected $pids;
    protected $next_run = false;

    public function __construct() {
        $this->pids = new \ArrayIterator();
    }

    public function isValid() {
        $check = array(
            is_executable($this->program),
            is_array($this->arguments),
            ($this->work_count >= self::WORK_COUNT_MIN or $this->work_count <= self::WORK_COUNT_MAX),
            ($this->schedule === false or $this->schedule instanceof CronExpression),
            ($this->timeout >= 0)
        );

        return !in_array(false, $check, true);
    }

    public function isScheduleTask() {
        return $this->schedule instanceof CronExpression;
    }

    public function getTotalPids() {
        return $this->pids->count();
    }

    public function run(\DateTime $now = null) {
        if (is_null($now)) {
            $now = new \DateTime();
        }

        $cron_now = $now;
        $cron_now->setTime($cron_now->format("H"), $cron_now->format("i"), 0);

        if ($this->isWorkCountReach()) {
            return false;
        }

        if ($this->isScheduleTask()) {
            $next_schedule_run = $this->schedule->getNextRunDate();

            #Daemon first start
            if ($this->next_run === false) {
                $this->setNextRun($next_schedule_run);
                return false;
            }

            #Next run not reach
            if ($cron_now < $this->next_run) {
                return false;
            }

            if ($cron_now == $this->next_run) {
                $this->setNextRun($next_schedule_run);
            }
        }

        $this->startTask($now);
    }

    public function killAll($signal = SIGTERM) {
        foreach ($this->pids as $p) {
            $p->kill($signal);
            $this->pids->offsetUnset($this->pids->key());
        }
    }

    public function killTimeout($signal = SIGTERM) {
        foreach ($this->pids as $p) {
            if ($p->isTimeout()) {
                $p->kill($signal);
                $this->pids->offsetUnset($this->pids->key());
            }
        }
    }

    public function killPid($pid, $signal = SIGTERM) {
        foreach ($this->pids as $p) {
            if ($p->getPid() === $pid) {
                $p->kill($signal);
                $this->pids->offsetUnset($this->pids->key());
            }
        }
    }

    public function setProgram($program) {
        if (!is_executable($program)) {
            throw new \Exception("Program must be executable");
        }
        $this->program = $program;
    }

    public function setArguments(array $arguments) {
        $this->arguments = $arguments;
    }

    public function setSchedule($schedule = false) {
        /*
         * One time execution ( can be loop )
         */
        if ($schedule === false) {
            return false;
        }

        if (!CronExpression::isValidExpression($schedule)) {
            throw new \Exception("Invalid schedule, must use cronjob format");
        }

        $this->schedule = CronExpression::factory($schedule);
    }

    public function setTimeout($timeout = 0) {
        $this->timeout = $timeout;
    }

    public function setPids(DaemonPidModel $pid) {
        if ($pid->isValid()) {
            $this->pids[] = $pid;
        }
    }

    public function setWorkCount($work_count = 1) {
        if ($work_count < self::WORK_COUNT_MIN or $work_count > self::WORK_COUNT_MAX) {
            throw new \Exception("Work count must be between 1 and 10");
        }
        $this->work_count = $work_count;
    }

    /*
     * Internal use only
     */

    protected function setNextRun(\DateTime $date) {
        $this->next_run = $date;
    }

    protected function isWorkCountReach() {
        return ($this->pids->count() >= $this->work_count);
    }

    protected function startTask(\DateTime $start_time) {
        $pid = pcntl_fork();
        if (!$pid) {
            pcntl_exec($this->program, $this->arguments);
            return false;
        }

        if ($this->timeout > 0) {
            $end_time = $start_time;
            $end_time->add(new \DateInterval(sprintf("PT%dS", $this->timeout)));
        } else {
            $end_time = null;
        }

        $this->pids->append(new DaemonPidModel($pid, $start_time, $end_time));
    }
}
