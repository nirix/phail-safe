<?php
/*
 * Phail Safe
 * Copyright (c) 2013-2014, J. Polgar
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Phail Safe nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS NOR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace PhailSafe;

/**
 * Test Group.
 *
 * @author J. Polgar
 */
class Group
{
    /**
     * Group name.
     *
     * @var string
     */
    protected $name;

    /**
     * @var Test[]
     */
    protected $tests = [];

    /**
     * @var string[]
     */
    protected $messages = [];

    /**
     * @param string   $name Group name.
     * @param callable $block
     */
    public function __construct($name, $block)
    {
        $this->name  = $name;
        $this->block = $block;
    }

    /**
     * Add test.
     *
     * @param string   $name Test name.
     * @param callable $block
     */
    public function test($name, $block)
    {
        $this->tests[] = new Test($name, $block);
    }

    /**
     * Execute tests.
     *
     * @return Group
     */
    public function execute()
    {
        $block = $this->block;
        $block($this);

        foreach ($this->tests as $test) {
            if (!$test->execute()) {
                echo 'F';
                $this->messages[] = $test->output();
            } else {
                echo '.';
            }
        }

        echo PHP_EOL;

        return $this;
    }

    /**
     * Display test messages.
     */
    public function display()
    {
        if (!count($this->messages)) {
            return;
        }

        echo PHP_EOL . $this->name . PHP_EOL;
        foreach ($this->messages as $message) {
            echo " - {$message}" . PHP_EOL;
        }
    }
}
