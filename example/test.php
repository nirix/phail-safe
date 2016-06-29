<?php
require __DIR__ . '/../src/TestSuite.php';
require __DIR__ . '/../src/Group.php';
require __DIR__ . '/../src/Test.php';
require __DIR__ . '/MyClass.php';

use PhailSafe\TestSuite;

TestSuite::tests(function ($suite) {
    $suite->group("MyClass", function ($g) {
        $myClass = new MyClass;

        $g->test("Be instance of [MyClass]", function ($t) use ($myClass) {
            $t->assertInstanceOf('MyClass', $myClass);
        });

        $g->test("Wrap text in []", function ($t) use ($myClass) {
            $t->assertEquals("[Hello!]", $myClass->wrap("Hello!"));
        });

        $g->test('Return true', function ($t) use ($myClass) {
            $t->assertTrue($myClass->getTrue());
        });

        $g->test('Return false', function ($t) use ($myClass) {
            $t->assertFalse($myClass->getFalse());
        });
    });
});
