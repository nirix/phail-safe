<?php
/*
 * Phail Safe
 * Copyright (c) 2013-2016, J. Polgar
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

use PhailSafe\TestSuite;
use PhailSafe\Test;

/**
 * Test Group.
 *
 * @package PhailSafe
 * @author  J. Polgar
 * @since   0.2.0
 */
class Group
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
     * @var TestSuite
     */
    protected $testSuite;

    /**
     * @var array
     */
    protected $tests = [];

    /**
     * @var integer
     */
    protected $testCount = 0;

    /**
     * @var integer
     */
    protected $assertionCount = 0;

    /**
     * @var integer
     */
    protected $failureCount = 0;

    /**
     * @var string   $name
     * @var callable $func
     */
    public function __construct($name, callable $func)
    {
        $this->name = $name;
        $this->func = $func;
    }

    /**
     * @param TestSuite $testSuite
     */
    public function setTestSuite(TestSuite $testSuite)
    {
        $this->testSuite = $testSuite;
    }

    /**
     * @return TestSuite
     */
    public function getTestSuite()
    {
        return $this->testSuite;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * Run the groups tests.
     */
    public function run()
    {
        $func = $this->func;
        $func($this);

        foreach ($this->tests as $test) {
            if ($this->testSuite->codeCoverageEnabled()) {
                $this->testSuite->getCodeCoverage()->start($this->name . ' / ' . $test->getName());
                $test->run();
                $this->testSuite->getCodeCoverage()->stop();
            } else {
                $test->run();
            }

            $this->failureCount = $this->failureCount + $test->getFailureCount();
            $this->assertionCount = $this->assertionCount + $test->getAssertionCount();
        }
    }

    /**
     * Create a new test.
     *
     * @param string   $name
     * @param callable $func
     *
     * @return Test
     */
    public function test($name, callable $func)
    {
        $this->testCount = $this->testCount + 1;

        $test = new Test($name, $func);
        $test->setGroup($this);
        $this->tests[] = $test;
        return $test;
    }

    /**
     * @return integer
     */
    public function getTestCount()
    {
        return $this->testCount;
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
}
