<?php
/*
 * Phail Safe
 * Copyright (c) 2013-2014, J Polgar
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
 * Test.
 *
 * @author J. Polgar
 */
class Test
{
    /**
     * Test name.
     *
     * @var string
     */
    protected $name;

    /**
     * Test errors.
     *
     * @var string[]
     */
    protected $errors = [];

    /**
     * @param string   $name Test name
     * @param callable
     */
    public function __construct($name, $block)
    {
        $this->name  = $name;
        $this->block = $block;
    }

    /**
     * Execute test assertions.
     */
    public function execute()
    {
        $block = $this->block;
        $block($this);
        return count($this->errors) ? false : true;
    }

    /**
     * @return string[]
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function output()
    {
        return "{$this->name}: {$this->errors[0]}";
    }

    /**
     * @param string|object $expected
     * @param object        $class
     */
    public function assertInstanceOf($expected, $class)
    {
        if (!$class instanceof $expected) {
            $expected = $this->varToString($expected);
            $class    = $this->varToString($class);

            $this->errors[] = sprintf("expected [%s] but got [%s]", $expected, $class);
        }
    }

    /**
     * @param mixed $expected
     * @param mixed $value
     */
    public function assertEqual($expected, $value)
    {
        if ($expected != $value) {
            $this->errors[] = sprintf("expected [%s] got [%s]", $this->varToString($expected), $this->varToString($value));
        }
    }

    /**
     * @param bool $value
     */
    public function assertTrue($value)
    {
        if (!$value === true) {
            $this->errors[] = sprintf("expected value to be true, was [%s]", $this->varToString($value));
        }
    }

    /**
     * @param bool $value
     */
    public function assertFalse($value)
    {
        if (!$value === false) {
            $this->errors[] = sprintf("expected value to be false, was [%s]", $this->varToString($value));
        }
    }

    /**
     * @param mixed $var
     */
    protected function varToString($var)
    {
        if (is_string($var) || is_numeric($var)) {
            return $var;
        } elseif (is_bool($var)) {
            return $var ? 'true' : 'false';
        } elseif (is_object($var)) {
            return get_class($var);
        }
    }
}
