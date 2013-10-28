<?php
/**
 * PHP Test
 * Copyright (c) 2013, J. Polgar
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of PHP Test nor the
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

namespace PhpTest;

class Test
{
    protected $description;
    protected $block;
    protected $passed = false;
    protected $messages = array();

    public function __construct($description, $block)
    {
        $this->description = $description;
        $this->block = $block;
    }

    public function run()
    {
        $block = $this->block;
        $block($this);
        return $this;
    }

    public function shouldEqual($should, $is)
    {
        $result = ($should === $is);
        $this->passed = $result;
        if (!$result) {
            $this->messages[] = "shouldEqual failed:";
            $this->messages[] = "  should: {$should}";
            $this->messages[] = "      is: {$is}";
        }
    }

    public function cliOutput()
    {
        print("{$this->description} => " . ($this->passed ? 'pass' : 'fail') . PHP_EOL);
        if ($this->passed) {

        } else {
            print(implode(PHP_EOL, $this->messages));
            print(PHP_EOL);
        }
    }
}
