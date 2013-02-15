<?php
namespace Contrib\Component\Inspector\Mock;

class ConcreteTestObject extends AbstractTestObject implements TestInterface
{
    // prop

    public $overrideProp;
    public $prop;

    // abstract

    public function abstractPublicMethod()
    {
    }

    protected function abstractProtectedMethod()
    {
    }

    // override

    public function overridePublicMethod()
    {
    }

    // interface

    public function publicInterfaceMethod()
    {
    }

}
