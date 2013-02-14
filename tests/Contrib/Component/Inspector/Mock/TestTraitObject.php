<?php
namespace Contrib\Component\Inspector\Mock;

class TestTraitObject
{
    use TestTrait;

    public function abstractPublicMethod()
    {
    }

    protected function abstractProtectedMethod()
    {
    }
}
