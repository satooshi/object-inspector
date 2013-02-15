<?php
namespace Contrib\Component\Inspector\Mock;

abstract class AbstractTestObject
{
    const ABSTRACT_CONSTANT = 'abstract constant';

    // prop

    public $abstractProp;
    public $overrideProp;

    // method

    abstract public function abstractPublicMethod();

    abstract protected function abstractProtectedMethod();

    public function inheritPublicMethod()
    {
    }

    public function overridePublicMethod()
    {
    }
}
