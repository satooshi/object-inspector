<?php
namespace Contrib\Component\Inspector\Mock;

abstract class AbstractTestObject
{
    const ABSTRACT_CONSTANT = 'abstract constant';

    abstract public function abstractPublicMethod();

    abstract protected function abstractProtectedMethod();
}
