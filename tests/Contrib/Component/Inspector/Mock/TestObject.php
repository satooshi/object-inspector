<?php
namespace Contrib\Component\Inspector\Mock;

class TestObject
{
    const CLASS_CONSTANT = 'class constant';

    public $publicProp;
    public $publicPropWithDefault = 'public prop';
    protected $protectedProp;
    protected $protectedPropWithDefault = 'protected prop';
    private $privateProp;
    private $privatePropWithDefualt = 'private prop';

    public static $publicStaticProp;
    public static $publicStaticPropWithDefault = 'public static prop';
    protected static $protectedStaticProp;
    protected static $protectedStaticPropWithDefault = 'protected static prop';
    private static $privateStaticProp;
    private static $privateStaticPropWithDefualt = 'private static prop';

    // public

    public function pulicMethod()
    {
    }

    public function publicMethodArgs($noType, array $arrayType, $defaultValue = 'default value')
    {
    }

    public function &publicMethodReturnReference()
    {
        return new \stdClass();
    }

    final public function pulicFinalMethod()
    {
    }

    public static function publicStaticMethod()
    {
    }

    // protected

    protected function protectedMethod()
    {
    }

    protected function protectedMethodArgs($noType, array $arrayType, $defaultValue = 'default value')
    {
    }

    protected function &protectedMethodReturnReference()
    {
        return new \stdClass();
    }

    protected static function protectedStaticMethod()
    {
    }

    // private

    private function privateMethod()
    {
    }

    private function privateMethodArgs($noType, array $arrayType, $defaultValue = 'default value')
    {
    }

    private function &privateMethodReturnReference()
    {
        return new \stdClass();
    }

    private static function privateStaticMethod()
    {
    }
}
