<?php
namespace Contrib\Component\Inspector\Mock;

trait TestTrait
{
    abstract public function abstractPublicMethod();
    abstract protected function abstractProtectedMethod();

    public function publicMethod()
    {
    }

    protected function protectedMethod()
    {
    }

    private function privateMethod()
    {
    }

    final public function finalPublicMethod()
    {
    }

    final protected function finalProtectedMethod()
    {
    }

    final private function finalPrivateMethod()
    {
    }
}
