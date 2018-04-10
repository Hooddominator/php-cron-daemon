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

class DaemonPidModel {

    protected $pid = 0;
    protected $last_run = null;
    protected $timeout = null;

    public function __construct($pid = 0, \DateTime $last_run = null, \DateTime $timeout = null) {
        $this->pid = $pid;
        $this->last_run = $last_run;
        $this->timeout = $timeout;
    }
    
    public function getPid() {
        return $this->pid;
    }
    
    public function kill($signal) {
        return posix_kill($this->pid, $signal);
    }

    public function isTimeout(\DateTime $now = null) {
        if (is_null($this->last_run) or is_null($this->timeout)) {
            return false;
        }

        if (is_null($now)) {
            $now = new \DateTime();
        }

        if ($now >= $this->timeout) {
            return true;
        }

        return false;
    }

}
