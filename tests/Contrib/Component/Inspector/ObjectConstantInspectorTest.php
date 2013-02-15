<?php
namespace Contrib\Component\Inspector;

require_once __DIR__ . '/Mock/TestObject.php';
require_once __DIR__ . '/Mock/SubTestObject.php';
require_once __DIR__ . '/Mock/AbstractTestObject.php';
require_once __DIR__ . '/Mock/TestInterface.php';
require_once __DIR__ . '/Mock/ExtendedInterface.php';
require_once __DIR__ . '/Mock/ConstTestInterface.php';
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

class ObjectConstantInspectorTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected function createObject()
    {
        $mock = new TestObject();
        $class = new \ReflectionClass($mock);

        return new ObjectConstantInspector($class);
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

        return new ObjectConstantInspector($class);
    }

    protected function createObjectWithFinalObject()
    {
        $mock = new FinalTestObject();
        $class = new \ReflectionClass($mock);

        return new ObjectConstantInspector($class);
    }

    protected function createObjectWithAbstractObject()
    {
        $mock = 'Contrib\Component\Inspector\Mock\AbstractTestObject';
        $class = new \ReflectionClass($mock);

        return new ObjectConstantInspector($class);
    }

    protected function createObjectWithInterface()
    {
        $mock = 'Contrib\Component\Inspector\Mock\TestInterface';
        $class = new \ReflectionClass($mock);

        return new ObjectConstantInspector($class);
    }

    protected function createObjectWithExtendedInterface()
    {
        $mock = 'Contrib\Component\Inspector\Mock\ExtendedInterface';
        $class = new \ReflectionClass($mock);

        return new ObjectConstantInspector($class);
    }

    protected function createObjectWithConstInterface()
    {
        $mock = 'Contrib\Component\Inspector\Mock\ConstTestInterface';
        $class = new \ReflectionClass($mock);

        return new ObjectConstantInspector($class);
    }

    protected function createObjectWithTraitObject()
    {
        if (PHP_VERSION >= '5.4') {
            $mock = new \Contrib\Component\Inspector\Mock\TestTraitObject();
            $class = new \ReflectionClass($mock);

            return new ObjectConstantInspector($class);
        }

        return null;
    }

    protected function createObjectWithTrait()
    {
        if (PHP_VERSION >= '5.4') {
            $mock = '\Contrib\Component\Inspector\Mock\TestTrait';
            $class = new \ReflectionClass($mock);

            return new ObjectConstantInspector($class);
        }

        return null;
    }

    protected function createObjectWithSubTrait()
    {
        if (PHP_VERSION >= '5.4') {
            $mock = '\Contrib\Component\Inspector\Mock\SubTestTrait';
            $class = new \ReflectionClass($mock);

            return new ObjectConstantInspector($class);
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

        $expected = 'constants';
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
    public function testObjectConstantTypeIsConstants()
    {
        $this->object = $this->createObject();

        $inspection = $this->getInspection();

        $expected = 'constants';

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(1, $inspection[$expected]);

        return $inspection[$expected];
    }

    // constants =>

    /**
     * @test
     * @depends testObjectConstantTypeIsConstants
     */
    public function testObjectConstantHasDeclaring($inspection)
    {
        $expected = 'declaring';

        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(1, $inspection[$expected]);

        return $inspection[$expected];
    }

    // constants => declaring =>

    /**
     * @test
     * @depends testObjectConstantHasDeclaring
     */
    public function testObjectConstantHasClassConstant($inspection)
    {
        $expected = 'CLASS_CONSTANT';

        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(1, $inspection[$expected]);

        return $inspection[$expected];
    }

    // constants => declaring => constant name =>

    /**
     * @test
     * @depends testObjectConstantHasClassConstant
     */
    public function testObjectConstantHasValue($inspection)
    {
        $expected = 'class constant';

        $this->assertArrayHasKey('value', $inspection);
        $this->assertEquals($expected, $inspection['value']);
    }

    // ConcreteTestObject
    // inherit

    /**
     * @test
     */
    public function concreteTestObjectConstantTypeIsConstants()
    {
        $this->object = $this->createObjectWithConcreteObject();

        $inspection = $this->getInspection();

        $expected = 'constants';

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(1, $inspection[$expected]);

        return $inspection[$expected];
    }

    // constants =>

    /**
     * @test
     * @depends concreteTestObjectConstantTypeIsConstants
     */
    public function concreteTestObjectConstantHasInherit($inspection)
    {
        $expected = 'inherit';

        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(1, $inspection[$expected]);

        return $inspection[$expected];
    }

    // constants => inherit =>

    /**
     * @test
     * @depends concreteTestObjectConstantHasInherit
     */
    public function concreteTestObjectConstantHasDeclaringClassName($inspection)
    {
        $className = 'Contrib\Component\Inspector\Mock\AbstractTestObject';
        $this->assertArrayHasKey($className, $inspection);
        $this->assertCount(1, $inspection[$className]);

        return $inspection[$className];
    }

    // constants => inherit => declaring class name =>

    /**
     * @test
     * @depends concreteTestObjectConstantHasDeclaringClassName
     */
    public function concreteTestObjectConstantHasClassConstant($inspection)
    {
        $expected = 'ABSTRACT_CONSTANT';

        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(2, $inspection[$expected]);

        return $inspection[$expected];
    }

    // constants => inherit => declaring class name => constant name =>

    /**
     * @test
     * @depends concreteTestObjectConstantHasClassConstant
     */
    public function concreteTestObjectConstantHasValue($inspection)
    {
        $expected = 'abstract constant';

        $this->assertArrayHasKey('value', $inspection);
        $this->assertEquals($expected, $inspection['value']);
    }

    /**
     * @test
     * @depends concreteTestObjectConstantHasClassConstant
     */
    public function concreteTestObjectConstantHasInheritClass($inspection)
    {
        $this->assertArrayHasKey('inherit', $inspection);
    }

    // SubTestObject
    // override

    /**
     * @test
     */
    public function subTestObjectConstantTypeIsConstants()
    {
        $this->object = $this->createObjectWithSubObject();

        $inspection = $this->getInspection();

        $expected = 'constants';

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(1, $inspection[$expected]);

        return $inspection[$expected];
    }

    // constants =>

    /**
     * @test
     * @depends subTestObjectConstantTypeIsConstants
     */
    public function subTestObjectConstantHasOverride($inspection)
    {
        $expected = 'override';

        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(1, $inspection[$expected]);

        return $inspection[$expected];
    }

    // constants => override =>

    /**
     * @test
     * @depends subTestObjectConstantHasOverride
     */
    public function subTestObjectConstantHasDeclaringClassName($inspection)
    {
        $className = 'Contrib\Component\Inspector\Mock\TestObject';
        $this->assertArrayHasKey($className, $inspection);
        $this->assertCount(1, $inspection[$className]);

        return $inspection[$className];
    }

    // constants => override => declaring class name =>

    /**
     * @test
     * @depends subTestObjectConstantHasDeclaringClassName
     */
    public function subTestObjectConstantHasClassConstant($inspection)
    {
        $expected = 'CLASS_CONSTANT';

        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(2, $inspection[$expected]);

        return $inspection[$expected];
    }

    // constants => override => declaring class name => constant name =>

    /**
     * @test
     * @depends subTestObjectConstantHasClassConstant
     */
    public function subTestObjectConstantHasValue($inspection)
    {
        $expected = 'override class constant';

        $this->assertArrayHasKey('value', $inspection);
        $this->assertEquals($expected, $inspection['value']);
    }

    /**
     * @test
     * @depends subTestObjectConstantHasClassConstant
     */
    public function subTestObjectConstantHasOverrideClass($inspection)
    {
        $this->assertArrayHasKey('override', $inspection);
    }

    // FinalTestObject

    /**
     * @test
     */
    public function finalTestObjectConstantTypeNotHasConstants()
    {
        $this->object = $this->createObjectWithFinalObject();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayNotHasKey('constants', $inspection);
    }

    // ConstTestInterface

    /**
     * @test
     */
    public function constTestInterfaceConstantTypeIsConstants()
    {
        $this->object = $this->createObjectWithConstInterface();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey('constants', $inspection);
        $this->assertCount(1, $inspection['constants']);

        return $inspection['constants'];
    }

    // constants =>

    /**
     * @test
     * @depends constTestInterfaceConstantTypeIsConstants
     */
    public function constTestInterfaceConstantHasDeclaring($inspection)
    {
        $expected = 'declaring';

        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(1, $inspection[$expected]);

        return $inspection[$expected];
    }

    // constants => declaring =>

    /**
     * @test
     * @depends constTestInterfaceConstantHasDeclaring
     */
    public function constTestInterfaceConstantHasClassConstant($inspection)
    {
        $expected = 'INTERFACE_CONST';

        $this->assertArrayHasKey($expected, $inspection);
        $this->assertCount(1, $inspection[$expected]);

        return $inspection[$expected];
    }

    // constants => declaring => constant name =>

    /**
     * @test
     * @depends constTestInterfaceConstantHasClassConstant
     */
    public function constTestInterfaceConstantHasValue($inspection)
    {
        $expected = 'interface const';

        $this->assertArrayHasKey('value', $inspection);
        $this->assertEquals($expected, $inspection['value']);
    }

    //TODO ConstInheritInterface
    //TODO ConstOverrideInterface
    //TODO TestTrait
    //TODO SubTestTrait
    //TODO InheritTestTrait
}
