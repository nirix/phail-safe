<?php
/*
 * Phail Safe
 * Copyright (c) 2013-2016, J Polgar
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

use PhailSafe\Group;

/**
 * Test.
 *
 * @package PhailSafe
 * @author  J. Polgar
 * @since   0.1.0
 */
class Test
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable
     */
    protected $func;

    /**
     * @var TestGroup
     */
    protected $testGroup;

    /**
     * @var array
     */
    protected $errorMessages = [];

    /**
     * @var integer
     */
    protected $failureCount = 0;

    /**
     * @var integer
     */
    protected $assertionCount = 0;

    /**
     * @param string   $name
     * @param callable $func
     */
    public function __construct($name, callable $func)
    {
        $this->name = $name;
        $this->func = $func;
    }

    /**
     * @param TestGroup $testGroup
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Run the test.
     */
    public function run()
    {
        $func = $this->func;
        $func($this);

        if ($this->failureCount === 0) {
            echo '.';
        } else {
            echo 'F';
        }
    }

    /**
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * @return integer
     */
    public function getAssertionCount()
    {
        return $this->assertionCount;
    }

    /**
     * @return integer
     */
    public function getFailureCount()
    {
        return $this->failureCount;
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
        } elseif (is_array($var)) {
            return 'Array(' . json_encode($var) . ')';
        } else {
            return (string) $var;
        }
    }

    /**
     * @param string $message
     */
    protected function addError()
    {
        $this->incrementFailureCount();
        $this->errorMessages[] = call_user_func_array('sprintf', func_get_args());
    }

    /**
     * @param integer $by
     */
    protected function incrementFailureCount($by = 1)
    {
        $this->failureCount = $this->failureCount + $by;
    }

    /**
     * @param integer $by
     */
    protected function incrementAssertionCount($by = 1)
    {
        $this->assertionCount = $this->assertionCount + $by;
    }

    // -------------------------------------------------------------------------
    // Assertions

    /**
     * @param mixed $value
     */
    public function assertTrue($value)
    {
        $this->incrementAssertionCount();

        if ($value !== true) {
            $this->addError('expected true but got [%s]', $this->varToString($value));
        }
    }

    /**
     * @param mixed $value
     */
    public function assertFalse($value)
    {
        $this->incrementAssertionCount();

        if ($value !== false) {
            $this->addError('expected false but got [%s]', $this->varToString($value));
        }
    }

    /**
     * @param mixed $expected
     * @param mixed $value
     */
    public function assertEquals($expected, $value)
    {
        $this->incrementAssertionCount();

        if ($expected !== $value) {
            $this->addError(
                'expected [%s] but got [%s]',
                $this->varToString($expected),
                $this->varToString($value)
            );
        }
    }

    /**
     * @param mixed $not
     * @param mixed $value
     */
    public function assertNotEquals($not, $value)
    {
        $this->incrementAssertionCount();

        if ($not === $value) {
            $this->addError(
                'expected [%s] to not be [%s]',
                $this->varToString($value),
                $this->varToString($not)
            );
        }
    }

    /**
     * @param mixed $value
     */
    public function assertArray($value)
    {
        $this->incrementAssertionCount();

        if (!is_array($value)) {
            $this->addError('expected an array but got [%s]', $this->varToString($value));
        }
    }

    /**
     * @param mixed $search
     * @param mixed $value
     */
    public function assertContains($search, $value, $shouldContain = true)
    {
        $this->incrementAssertionCount();

        $valueType = gettype($value);
        $searchFound = false;

        switch ($valueType) {
            case 'NULL':
                return $this->addError('unable to search NULL for [%s]', $this->varToString($search));
                break;

            case 'string':
                if (strpos($value, $search) !== false) {
                    $searchFound = true;
                }
                break;

            case 'object':
                if (method_exists($value, '__toString') && strpos((string) $value, $search) !== false) {
                    $searchFound = true;
                } else {
                    throw new Exception(sprintf(
                        'Unable to check if object [%s] contains value [%s]',
                        get_class($value),
                        $this->varToString($search)
                    ));
                }
                break;

            case 'array':
                if (in_array($search, $value)) {
                    $searchFound = true;
                }
                break;

            default:
                throw new Exception(sprintf(
                    'Test::assertContains() doesn\'t support the type of value passed [%s]',
                    gettype($value)
                ));
        }

        if (!$searchFound && $shouldContain) {
            $this->addError(
                'expected [%s] to contain [%s]',
                $this->varToString($value),
                $this->varToString($search)
            );
        } elseif ($searchFound && !$shouldContain) {
            $this->addError(
                'expected [%s] to not contain [%s]',
                $this->varToString($value),
                $this->varToString($search)
            );
        }
    }

    /**
     * @param mixed $search
     * @param mixed $value
     */
    public function assertNotContains($search, $value)
    {
        return $this->assertContains($search, $value, false);
    }

    /**
     * @param mixed $expected
     * @param mixed $value
     */
    public function assertInstanceOf($expected, $value)
    {
        $this->incrementAssertionCount();

        if (!($value instanceof $expected)) {
            $this->addError(
                'expected instance of [%s] but was [%s]',
                $this->varToString($search),
                $this->varToString($value)
            );
        }
    }

    /**
     * @param mixed $class
     * @param mixed $value
     */
    public function assertNotInstanceOf($class, $value)
    {
        $this->incrementAssertionCount();

        if ($value instanceof $expected) {
            $this->addError(
                'expected [%s] to not be an instance of [%s]',
                $this->varToString($value),
                $this->varToString($class)
            );
        }
    }
}
