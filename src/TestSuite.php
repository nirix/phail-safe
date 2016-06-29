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

use PhailSafe\Group;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as CodeCoverageHtmlFacade;

/**
 * Test Suite.
 *
 * @package PhailSafe
 * @author  J. Polgar
 * @since   0.2.0
 */
class TestSuite
{
    /**
     * PhailSafe version.
     */
    const VERSION = '0.3.0';

    /**
     * @var TestSuite
     */
    protected static $instance;

    /**
     * @var Group[]
     */
    protected $groups = [];

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
     * @var CodeCoverage
     */
    protected $codeCoverage;

    /**
     * Whether or not if code coverage is enabled.
     *
     * @var boolean
     */
    protected $codeCoverageEnabled = false;

    /**
     * Directory to output code coverage report.
     *
     * @var string
     */
    protected $coverageOutputDirectory;

    public static function tests(callable $func)
    {
        $func(static::getInstance());
    }

    public static function getInstance()
    {
        return static::$instance ?: new static;
    }

    public static function group($name, callable $func)
    {
        return static::getInstance()->newGroup($name, $func);
    }

    protected function __construct()
    {
        global $argv;

        static::$instance = $this;

        $codeCoverageKey = array_search('--code-coverage', $argv);

        if ($codeCoverageKey) {
            if (class_exists('\SebastianBergmann\CodeCoverage\CodeCoverage')) {
                $coverageDirectoryKey = $codeCoverageKey + 1;
                $coverageOutputDirectory = isset($argv[$coverageDirectoryKey])
                                           ? $argv[$coverageDirectoryKey]
                                           : 'tmp/code-coverage-report';

                $this->codeCoverageEnabled = true;
                $this->coverageOutputDirectory = $coverageOutputDirectory;
                $this->codeCoverage = new CodeCoverage;
            }
        }
    }

    /**
     * Run test suite.
     */
    public function run()
    {
        echo 'PhailSafe v' . static::VERSION . ' by Nirix (https://nirix.net)', PHP_EOL, PHP_EOL;

        foreach ($this->groups as $group) {
            $group->run();

            $this->testCount = $this->testCount + $group->getTestCount();
            $this->assertionCount = $this->assertionCount + $group->getAssertionCount();
            $this->failureCount = $this->failureCount + $group->getFailureCount();
        }

        echo PHP_EOL;

        if ($this->failureCount) {
            echo PHP_EOL;

            foreach ($this->groups as $group) {
                if ($group->getFailureCount() > 0) {
                    echo $group->getName() . PHP_EOL;

                    foreach ($group->getTests() as $test) {
                        if ($test->getFailureCount()) {
                            echo '  - ', $test->getName(), PHP_EOL;

                            foreach ($test->getErrorMessages() as $message) {
                                echo '      - ', $message, PHP_EOL;
                            }
                        }
                    }
                }
            }
        }

        printf(
            PHP_EOL . 'Ran %d tests with %d assertions and %d failures' . PHP_EOL,
            $this->testCount,
            $this->assertionCount,
            $this->failureCount
        );

        if ($this->codeCoverageEnabled) {
            echo PHP_EOL . 'Generating code coverage report..' . PHP_EOL;
            $writer = new CodeCoverageHtmlFacade;
            $writer->process($this->codeCoverage, $this->coverageOutputDirectory);
        }

        exit($this->failureCount > 0 ? 1 : 0);
    }

    /**
     * @return boolean
     */
    public function codeCoverageEnabled()
    {
        return $this->codeCoverageEnabled;
    }

    /**
     * @return CodeCoverage
     */
    public function getCodeCoverage()
    {
        return $this->codeCoverage;
    }

    /**
     * Create test group.
     *
     * @param string   $name
     * @param callable $func
     *
     * @return TestGroup
     */
    public function newGroup($name, callable $func)
    {
        $group = new Group($name, $func);
        $group->setTestSuite($this);
        $this->groups[] = $group;
        return $group;
    }

    public function __destruct()
    {
        $this->run();
    }
}
