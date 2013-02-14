<?php
namespace Contrib\Component\Inspector;

require_once __DIR__ . '/Mock/TestObject.php';
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
use Contrib\Component\Inspector\Mock\ConcreteTestObject;
use Contrib\Component\Inspector\Mock\FinalTestObject;

class ObjectTypeInspectorTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected function createObject()
    {
        $mock = new TestObject();
        $class = new \ReflectionClass($mock);

        return new ObjectTypeInspector($class);
    }

    protected function createObjectWithConcreteObject()
    {
        $mock = new ConcreteTestObject();
        $class = new \ReflectionClass($mock);

        return new ObjectTypeInspector($class);
    }

    protected function createObjectWithFinalObject()
    {
        $mock = new FinalTestObject();
        $class = new \ReflectionClass($mock);

        return new ObjectTypeInspector($class);
    }

    protected function createObjectWithAbstractObject()
    {
        $mock = 'Contrib\Component\Inspector\Mock\AbstractTestObject';
        $class = new \ReflectionClass($mock);

        return new ObjectTypeInspector($class);
    }

    protected function createObjectWithInterface()
    {
        $mock = 'Contrib\Component\Inspector\Mock\TestInterface';
        $class = new \ReflectionClass($mock);

        return new ObjectTypeInspector($class);
    }

    protected function createObjectWithExtendedInterface()
    {
        $mock = 'Contrib\Component\Inspector\Mock\ExtendedInterface';
        $class = new \ReflectionClass($mock);

        return new ObjectTypeInspector($class);
    }

    protected function createObjectWithTraitObject()
    {
        if (PHP_VERSION >= '5.4') {
            $mock = new \Contrib\Component\Inspector\Mock\TestTraitObject();
            $class = new \ReflectionClass($mock);

            return new ObjectTypeInspector($class);
        }

        return null;
    }

    protected function createObjectWithTrait()
    {
        if (PHP_VERSION >= '5.4') {
            $mock = '\Contrib\Component\Inspector\Mock\TestTrait';
            $class = new \ReflectionClass($mock);

            return new ObjectTypeInspector($class);
        }

        return null;
    }

    protected function createObjectWithSubTrait()
    {
        if (PHP_VERSION >= '5.4') {
            $mock = '\Contrib\Component\Inspector\Mock\SubTestTrait';
            $class = new \ReflectionClass($mock);

            return new ObjectTypeInspector($class);
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

        $expected = 'class';
        $actual = $this->object->getInspectionName();

        $this->assertEquals($expected, $actual);
    }

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
    public function testObjectTypeIsClass()
    {
        $this->object = $this->createObject();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey('class', $inspection);

        return $inspection['class'];
    }

    /**
     * @test
     * @depends testObjectTypeIsClass
     */
    public function testObjectHasNamespace($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock';

        $this->assertArrayHasKey('namespace', $inspection);
        $this->assertEquals($expected, $inspection['namespace']);
    }

    /**
     * @test
     * @depends testObjectTypeIsClass
     */
    public function testObjectHasShortName($inspection)
    {
        $expected = 'TestObject';

        $this->assertArrayHasKey('shortname', $inspection);
        $this->assertEquals($expected, $inspection['shortname']);
    }

    /**
     * @test
     * @depends testObjectTypeIsClass
     */
    public function testObjectHasName($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock\TestObject';

        $this->assertArrayHasKey('name', $inspection);
        $this->assertEquals($expected, $inspection['name']);
    }

    /**
     * @test
     * @depends testObjectTypeIsClass
     */
    public function testObjectHasFileName($inspection)
    {
        $expected = __DIR__ . '/Mock/TestObject.php';

        $this->assertArrayHasKey('filename', $inspection);
        $this->assertEquals($expected, $inspection['filename']);
    }

    /**
     * @test
     * @depends testObjectTypeIsClass
     */
    public function testObjectNotHaveModifier($inspection)
    {
        $this->assertArrayNotHasKey('modifier', $inspection);
    }

    /**
     * @test
     * @depends testObjectTypeIsClass
     */
    public function testObjectIsNotExtend($inspection)
    {
        $this->assertArrayNotHasKey('extends', $inspection);
    }

    /**
     * @test
     * @depends testObjectTypeIsClass
     */
    public function testObjectIsNotImplement($inspection)
    {
        $this->assertArrayNotHasKey('implements', $inspection);
    }

    /**
     * @test
     * @depends testObjectTypeIsClass
     */
    public function testObjectNotUseTrait($inspection)
    {
        $this->assertArrayNotHasKey('use', $inspection);
    }

    // extended, implemented concrete class

    /**
     * @test
     */
    public function concreteTestObjectTypeIsClass()
    {
        $this->object = $this->createObjectWithConcreteObject();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey('class', $inspection);

        return $inspection['class'];
    }

    /**
     * @test
     * @depends concreteTestObjectTypeIsClass
     */
    public function concreteTestObjectHasNamespace($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock';

        $this->assertArrayHasKey('namespace', $inspection);
        $this->assertEquals($expected, $inspection['namespace']);
    }

    /**
     * @test
     * @depends concreteTestObjectTypeIsClass
     */
    public function concreteTestObjectHasShortname($inspection)
    {
        $expected = 'ConcreteTestObject';

        $this->assertArrayHasKey('shortname', $inspection);
        $this->assertEquals($expected, $inspection['shortname']);
    }

    /**
     * @test
     * @depends concreteTestObjectTypeIsClass
     */
    public function concreteTestObjectHasName($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock\ConcreteTestObject';

        $this->assertArrayHasKey('name', $inspection);
        $this->assertEquals($expected, $inspection['name']);
    }

    /**
     * @test
     * @depends concreteTestObjectTypeIsClass
     */
    public function concreteTestObjectHasFilename($inspection)
    {
        $expected = __DIR__ . '/Mock/ConcreteTestObject.php';

        $this->assertArrayHasKey('filename', $inspection);
        $this->assertEquals($expected, $inspection['filename']);
    }

    /**
     * @test
     * @depends concreteTestObjectTypeIsClass
     */
    public function concreteTestObjectNotHaveModifier($inspection)
    {
        $this->assertArrayNotHasKey('modifier', $inspection);
    }

    // extended abstract class

    /**
     * @test
     * @depends concreteTestObjectTypeIsClass
     */
    public function concreteTestObjectIsExtended($inspection)
    {
        $this->assertArrayHasKey('extends', $inspection);

        return $inspection['extends'];
    }

    /**
     * @test
     * @depends concreteTestObjectIsExtended
     */
    public function extendedObjectHasShortname($inspection)
    {
        $expected = 'AbstractTestObject';

        $this->assertArrayHasKey('shortname', $inspection);
        $this->assertEquals($expected, $inspection['shortname']);
    }

    /**
     * @test
     * @depends concreteTestObjectIsExtended
     */
    public function extendedObjectHasNamespace($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock';

        $this->assertArrayHasKey('namespace', $inspection);
        $this->assertEquals($expected, $inspection['namespace']);
    }

    /**
     * @test
     * @depends concreteTestObjectIsExtended
     */
    public function extendedObjectHasName($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock\AbstractTestObject';

        $this->assertArrayHasKey('name', $inspection);
        $this->assertEquals($expected, $inspection['name']);
    }

    /**
     * @test
     * @depends concreteTestObjectIsExtended
     */
    public function extendedObjectHasFilename($inspection)
    {
        $expected = __DIR__ . '/Mock/AbstractTestObject.php';

        $this->assertArrayHasKey('filename', $inspection);
        $this->assertEquals($expected, $inspection['filename']);
    }

    /**
     * @test
     * @depends concreteTestObjectIsExtended
     */
    public function extendedObjectIsUserDefined($inspection)
    {
        $this->assertArrayHasKey('userDefined', $inspection);
        $this->assertTrue($inspection['userDefined']);
    }

    // implemented interface

    /**
     * @test
     * @depends concreteTestObjectTypeIsClass
     */
    public function concreteTestObjectIsImplemented($inspection)
    {
        $this->assertArrayHasKey('implements', $inspection);
        $this->assertCount(1, $inspection['implements']);

        return $inspection['implements'][0];
    }

    /**
     * @test
     * @depends concreteTestObjectIsImplemented
     */
    public function implementedObjectHasShortname($inspection)
    {
        $expected = 'TestInterface';

        $this->assertArrayHasKey('shortname', $inspection);
        $this->assertEquals($expected, $inspection['shortname']);
    }

    /**
     * @test
     * @depends concreteTestObjectIsImplemented
     */
    public function implementedObjectHasNamespace($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock';

        $this->assertArrayHasKey('namespace', $inspection);
        $this->assertEquals($expected, $inspection['namespace']);
    }

    /**
     * @test
     * @depends concreteTestObjectIsImplemented
     */
    public function implementedObjectHasName($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock\TestInterface';

        $this->assertArrayHasKey('name', $inspection);
        $this->assertEquals($expected, $inspection['name']);
    }

    /**
     * @test
     * @depends concreteTestObjectIsImplemented
     */
    public function implementedObjectHasFilename($inspection)
    {
        $expected = __DIR__ . '/Mock/TestInterface.php';

        $this->assertArrayHasKey('filename', $inspection);
        $this->assertEquals($expected, $inspection['filename']);
    }

    /**
     * @test
     * @depends concreteTestObjectIsImplemented
     */
    public function implementedObjectIsUserDefined($inspection)
    {
        $this->assertArrayHasKey('userDefined', $inspection);
        $this->assertTrue($inspection['userDefined']);
    }

    // abstract class

    /**
     * @test
     */
    public function abstractTestObjectTypeIsClass()
    {
        $this->object = $this->createObjectWithAbstractObject();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey('class', $inspection);

        return $inspection['class'];
    }

    /**
     * @test
     * @depends abstractTestObjectTypeIsClass
     */
    public function abstractTestObjectHasModifierAbstract($inspection)
    {
        $expected = 'abstract';

        $this->assertArrayHasKey('modifier', $inspection);
        $this->assertEquals($expected, $inspection['modifier']);
    }

    // final class

    /**
     * @test
     */
    public function finalTestObjectTypeIsClass()
    {
        $this->object = $this->createObjectWithFinalObject();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey('class', $inspection);

        return $inspection['class'];
    }

    /**
     * @test
     * @depends finalTestObjectTypeIsClass
     */
    public function finalTestObjectHasModifierFinal($inspection)
    {
        $expected = 'final';

        $this->assertArrayHasKey('modifier', $inspection);
        $this->assertEquals($expected, $inspection['modifier']);
    }

    // interface

    /**
     * @test
     */
    public function testInterfaceTypeIsClass()
    {
        $this->object = $this->createObjectWithInterface();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey('class', $inspection);

        return $inspection['class'];
    }

    /**
     * @test
     * @depends testInterfaceTypeIsClass
     */
    public function testInterfaceNotHaveModifier($inspection)
    {
        $this->assertArrayNotHasKey('modifier', $inspection);
    }

    // extends interface

    /**
     * @test
     */
    public function extendedInterfaceTypeIsClass()
    {
        $this->object = $this->createObjectWithExtendedInterface();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey('class', $inspection);

        return $inspection['class'];
    }

    /**
     * @test
     * @depends extendedInterfaceTypeIsClass
     */
    public function extendedInterfaceNotHaveModifier($inspection)
    {
        $this->assertArrayNotHasKey('modifier', $inspection);
    }

    /**
     * @test
     * @depends extendedInterfaceTypeIsClass
     */
    public function extendedInterfaceIsExtended($inspection)
    {
        $this->assertArrayHasKey('extends', $inspection);
    }

    /**
     * @test
     * @depends extendedInterfaceTypeIsClass
     */
    public function extendedInterfaceIsNotImplement($inspection)
    {
        $this->assertArrayNotHasKey('implements', $inspection);
    }

    /**
     * @test
     * @depends extendedInterfaceTypeIsClass
     */
    public function extendedInterfaceNotUseTrait($inspection)
    {
        $this->assertArrayNotHasKey('use', $inspection);
    }

    // trait object

    /**
     * @test
     * @requires PHP 5.4
     */
    public function testTraitObjectTypeIsClass()
    {
        $this->object = $this->createObjectWithTraitObject();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey('class', $inspection);

        return $inspection['class'];
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends testTraitObjectTypeIsClass
     */
    public function testTraitObjectUseTrait($inspection)
    {
        $this->assertArrayHasKey('use', $inspection);
        $this->assertCount(1, $inspection['use']);

        return $inspection['use'][0];
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends testTraitObjectUseTrait
     */
    public function usedTestTraitByObjectHasShortname($inspection)
    {
        $expected = 'TestTrait';

        $this->assertArrayHasKey('shortname', $inspection);
        $this->assertEquals($expected, $inspection['shortname']);
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends testTraitObjectUseTrait
     */
    public function usedTestTraitByObjectHasNamespace($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock';

        $this->assertArrayHasKey('namespace', $inspection);
        $this->assertEquals($expected, $inspection['namespace']);
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends testTraitObjectUseTrait
     */
    public function usedTestTraitByObjectHasName($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock\TestTrait';

        $this->assertArrayHasKey('name', $inspection);
        $this->assertEquals($expected, $inspection['name']);
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends testTraitObjectUseTrait
     */
    public function usedTestTraitByObjectHasFilename($inspection)
    {
        $expected = __DIR__ . '/Mock/TestTrait.php';

        $this->assertArrayHasKey('filename', $inspection);
        $this->assertEquals($expected, $inspection['filename']);
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends testTraitObjectUseTrait
     */
    public function usedTestTraitByObjectIsUserDefined($inspection)
    {
        $this->assertArrayHasKey('userDefined', $inspection);
        $this->assertTrue($inspection['userDefined']);
    }

    // trait

    /**
     * @test
     * @requires PHP 5.4
     */
    public function testTraitTypeIsClass()
    {
        $this->object = $this->createObjectWithTrait();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey('class', $inspection);

        return $inspection['class'];
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends testTraitTypeIsClass
     */
    public function testTraitHasModifierAbstractPublic($inspection)
    {
        $expected = 'abstract public';

        $this->assertArrayHasKey('modifier', $inspection);
        $this->assertEquals($expected, $inspection['modifier']);
    }

    // sub trait

    /**
     * @test
     * @requires PHP 5.4
     */
    public function subTestTraitTypeIsClass()
    {
        $this->object = $this->createObjectWithSubTrait();

        $inspection = $this->getInspection();

        $this->assertTrue(is_array($inspection));
        $this->assertArrayHasKey('class', $inspection);

        return $inspection['class'];
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends subTestTraitTypeIsClass
     */
    public function subTestTraitHasModifierAbstractPublic($inspection)
    {
        $expected = 'abstract public';

        $this->assertArrayHasKey('modifier', $inspection);
        $this->assertEquals($expected, $inspection['modifier']);
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends subTestTraitTypeIsClass
     */
    public function subTestTraitUseTrait($inspection)
    {
        $this->assertArrayHasKey('use', $inspection);
        $this->assertCount(1, $inspection['use']);

        return $inspection['use'][0];
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends subTestTraitUseTrait
     */
    public function usedTestTraitByTraitHasShortname($inspection)
    {
        $expected = 'TestTrait';

        $this->assertArrayHasKey('shortname', $inspection);
        $this->assertEquals($expected, $inspection['shortname']);
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends subTestTraitUseTrait
     */
    public function usedTestTraitByTraitHasNamespace($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock';

        $this->assertArrayHasKey('namespace', $inspection);
        $this->assertEquals($expected, $inspection['namespace']);
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends subTestTraitUseTrait
     */
    public function usedTestTraitByTraitHasName($inspection)
    {
        $expected = 'Contrib\Component\Inspector\Mock\TestTrait';

        $this->assertArrayHasKey('name', $inspection);
        $this->assertEquals($expected, $inspection['name']);
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends subTestTraitUseTrait
     */
    public function usedTestTraitByTraitHasFilename($inspection)
    {
        $expected = __DIR__ . '/Mock/TestTrait.php';

        $this->assertArrayHasKey('filename', $inspection);
        $this->assertEquals($expected, $inspection['filename']);
    }

    /**
     * @test
     * @requires PHP 5.4
     * @depends subTestTraitUseTrait
     */
    public function usedTestTraitByTraitIsUserDefined($inspection)
    {
        $this->assertArrayHasKey('userDefined', $inspection);
        $this->assertTrue($inspection['userDefined']);
    }

}
