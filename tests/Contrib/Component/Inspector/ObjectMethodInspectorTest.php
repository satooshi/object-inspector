<?php
namespace Contrib\Component\Inspector;

require_once __DIR__ . '/Mock/TestObject.php';
require_once __DIR__ . '/Mock/SubTestObject.php';
require_once __DIR__ . '/Mock/AbstractTestObject.php';
require_once __DIR__ . '/Mock/TestInterface.php';
require_once __DIR__ . '/Mock/ExtendedInterface.php';
require_once __DIR__ . '/Mock/ConcreteTestObject.php';
require_once __DIR__ . '/Mock/FinalTestObject.php';

if (PHP_VERSION >= '5.4') {
    require_once __DIR__ . '/Mock/TestTrait.php';
    require_once __DIR__ . '/Mock/SubTestTrait.php';
    require_once __DIR__ . '/Mock/TestTraitObject.php';
}

use Contrib\Component\Inspector\Mock\TestObject;
use Contrib\Component\Inspector\Mock\SubTestObject;
use Contrib\Component\Inspector\Mock\ConcreteTestObject;
use Contrib\Component\Inspector\Mock\FinalTestObject;

class ObjectMethodInspectorTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected function createObject()
    {
        $mock = new TestObject();
        $class = new \ReflectionClass($mock);

        return new ObjectMethodInspector($class);
    }

    protected function createObjectWithSubObject()
    {
        $mock = new SubTestObject();
        $class = new \ReflectionClass($mock);

        return new ObjectConstantInspector($class);
    }

    protected function createObjectWithConcreteObject()
    {
        $mock = new ConcreteTestObject();
        $class = new \ReflectionClass($mock);

        return new ObjectMethodInspector($class);
    }

    protected function createObjectWithFinalObject()
    {
        $mock = new FinalTestObject();
        $class = new \ReflectionClass($mock);

        return new ObjectMethodInspector($class);
    }

    protected function createObjectWithAbstractObject()
    {
        $mock = 'Contrib\Component\Inspector\Mock\AbstractTestObject';
        $class = new \ReflectionClass($mock);

        return new ObjectMethodInspector($class);
    }

    protected function createObjectWithInterface()
    {
        $mock = 'Contrib\Component\Inspector\Mock\TestInterface';
        $class = new \ReflectionClass($mock);

        return new ObjectMethodInspector($class);
    }

    protected function createObjectWithExtendedInterface()
    {
        $mock = 'Contrib\Component\Inspector\Mock\ExtendedInterface';
        $class = new \ReflectionClass($mock);

        return new ObjectMethodInspector($class);
    }

    protected function createObjectWithTraitObject()
    {
        if (PHP_VERSION >= '5.4') {
            $mock = new \Contrib\Component\Inspector\Mock\TestTraitObject();
            $class = new \ReflectionClass($mock);

            return new ObjectMethodInspector($class);
        }

        return null;
    }

    protected function createObjectWithTrait()
    {
        if (PHP_VERSION >= '5.4') {
            $mock = '\Contrib\Component\Inspector\Mock\TestTrait';
            $class = new \ReflectionClass($mock);

            return new ObjectMethodInspector($class);
        }

        return null;
    }

    protected function createObjectWithSubTrait()
    {
        if (PHP_VERSION >= '5.4') {
            $mock = '\Contrib\Component\Inspector\Mock\SubTestTrait';
            $class = new \ReflectionClass($mock);

            return new ObjectMethodInspector($class);
        }

        return null;
    }

    protected function getInspection()
    {
        $this->object->inspect();

        return $this->object->getInspection();
    }

    // getInspectionName()

    /**
     * @test
     */
    public function inspectionNameIsClass()
    {
        $this->object = $this->createObject();

        $expected = 'methods';
        $actual = $this->object->getInspectionName();

        $this->assertEquals($expected, $actual);
    }

    // inspect()
    // getInspection()

    // plain object

    /**
     * @test
     */
    public function inspectionIsEmptyBeforeInspect()
    {
        $this->object = $this->createObject();

        $actual = $this->object->getInspection();

        $this->assertEmpty($actual);
    }

    /**
     * @test
     */
    public function testObjectTypeHasMethod()
    {
        $this->object = $this->createObject();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey('methods', $inspection);

        return $inspection['methods'];
    }


    // ConcreteTestObject
    // inherit

    /**
     * @test
     */
    public function concreteTestObjectConstantTypeIsMethod()
    {
        $this->object = $this->createObjectWithConcreteObject();

        $inspection = $this->getInspection();

        $expected = 'methods';

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(3, $inspection[$expected]);

        return $inspection[$expected];
    }


    // SubTestObject
    // override

    /**
     */
    public function subTestObjectConstantTypeIsConstants()
    {
        $this->object = $this->createObjectWithSubObject();

        $inspection = $this->getInspection();

        $expected = 'methods';

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(1, $inspection[$expected]);

        return $inspection[$expected];
    }

}
