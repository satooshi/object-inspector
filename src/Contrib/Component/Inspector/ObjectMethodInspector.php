<?php
namespace Contrib\Component\Inspector;

/**
 * Inspector for object method.
 *
 * The inspection result contains:
 *
 * * method name: string
 *     * name: string
 *     * modifier: string
 *     * inherit: array
 *     * implement: array
 *     * override: array
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class ObjectMethodInspector extends ObjectInspector
{
    /**
     * Inspection name.
     *
     * @var string
     */
    protected $inspectionName = 'methods';

    /**
     * ReflectionClass.
     *
     * @var \ReflectionClass
     */
    protected $class;

    /**
     * ReflectionMethod array.
     *
     * @var \ReflectionMethod[]
     */
    protected $methods = array();

    /**
     * Constructor.
     *
     * @param  \ReflectionClass $class ReflectionClass object.
     * @param array $options Configuration options.
     */
    public function __construct(\ReflectionClass $class, array $options = array())
    {
        parent::__construct($options);

        $this->class = $class;
        $this->methods = $class->getMethods();

        usort(
            $this->methods,
            function ($method1, $method2) {
                if ($method1->name === $method2->name) {
                    return 0;
                }

                return $method1->name < $method2->name ? -1 : 1;
            }
        );
    }

    // API

    /**
     * {@inheritdoc}
     *
     * @see \Contrib\Component\Inspector\ObjectInspectorInterface::inspect()
     */
    public function inspect()
    {
        $methodList = static::sortByModifier($this->methods);

        foreach ($methodList as $modifiers) {
            foreach ($modifiers as $method) {
                $methodName = $method->getName();

                if ($method->returnsReference()) {
                    $this->inspection[$methodName]['name'] = $this->referenceIndicator . $methodName;
                } else {
                    $this->inspection[$methodName]['name'] = $methodName;
                }

                $this->inspection[$methodName]['modifier'] = implode(' ', \Reflection::getModifierNames($method->getModifiers()));

                $params = $method->getParameters();

                if (!empty($params)) {
                    $this->inspection[$methodName] = array_merge($this->inspection[$methodName], $this->getParametersInspection($params));
                }

                if ($this->isInheritMethod($method, $declaringClass)) {
                    $this->inspection[$methodName]['inherit'] = $this->getInheritanceInspection($declaringClass);
                } elseif ($this->isImplementMethod($method, $declaringClass)) {
                    $this->inspection[$methodName]['implement'] = $this->getInheritanceInspection($declaringClass);
                } elseif ($this->isOverrideMethod($method, $declaringClass)) {
                    $this->inspection[$methodName]['override'] = $this->getInheritanceInspection($declaringClass);
                }
            }
        }

        $this->inspection = static::sortByInheritance($this->inspection);
    }

    // internal method

    /**
     * Sorts method inspection by modifier order.
     *
     * @param \ReflectionMethod[] $methods Method inspection.
     * @return array Sorted inspection.
     */
    protected static function sortByModifier(array $methods)
    {
        $sortedByModifier = array();

        foreach ($methods as $method) {
            if ($method instanceof \ReflectionMethod) {
                $sortedByModifier[$method->getModifiers()][] = $method;
            }
        }

        ksort($sortedByModifier);

        return $sortedByModifier;
    }

    /**
     * Sorts inspection by inheritance order.
     *
     * @param array $inspection Inspection info.
     * @return array Sorted inspection.
     */
    protected static function sortByInheritance(array $inspection)
    {
        $sortedByInheritance = array();

        foreach ($inspection as $name => $method) {
            if (isset($method['inherit'])) {
                $methodName = $method['inherit']['name'];
                $sortedByInheritance['inherit'][$methodName][$name] = $method;
            } elseif (isset($method['implement'])) {
                $methodName = $method['implement']['name'];
                $sortedByInheritance['implement'][$methodName][$name] = $method;
            } elseif (isset($method['override'])) {
                $methodName = $method['override']['name'];
                $sortedByInheritance['override'][$methodName][$name] = $method;
            } else {
                $sortedByInheritance['declaring'][$name] = $method;
            }
        }

        return $sortedByInheritance;
    }

    /**
     * Returns method parameters inspection.
     *
     * @param array $params Method parameters to be inspected.
     * @return array Inspection info.
     */
    protected function getParametersInspection(array $params)
    {
        $inspector = new ObjectMethodParameterInspector($params);
        $inspector->inspect();

        return $inspector->getInspection();
    }

    /**
     * Returns whether the method is inherited from parent.
     *
     * @param \ReflectionMethod $method          ReflectionMethod object.
     * @param \ReflectionClass  &$declaringClass Output parameter for Declaring class.
     * @return bool true if the method is inherited from parent, false otherwise.
     */
    protected function isInheritMethod(\ReflectionMethod $method, &$declaringClass)
    {
        $declaringClass = $method->getDeclaringClass();

        return $this->class->getName() !== $method->getDeclaringClass()->getName();
    }

    /**
     * Returns whether the method is implement method.
     *
     * @param \ReflectionMethod $method          ReflectionMethod object.
     * @param \ReflectionClass  &$declaringClass Output parameter for Declaring class.
     * @return bool true if the method is implement method, false otherwise.
     */
    protected function isImplementMethod(\ReflectionMethod $method, &$declaringClass)
    {
        try {
            $declaringClass = $method->getPrototype()->getDeclaringClass();

            if ($declaringClass->isInterface()) {
                return true;
            }

            if ($declaringClass->getMethod($method->getName())->isAbstract()) {
                return true;
            }

            return false;
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * Returns whether the method is overriden from parent.
     *
     * @param \ReflectionMethod $method          ReflectionMethod object.
     * @param \ReflectionClass  &$declaringClass Output parameter for Declaring class.
     * @return true if the method is overriden from parent, false otherwise.
     */
    protected function isOverrideMethod(\ReflectionMethod $method, &$declaringClass)
    {
        $implement = $this->isImplementMethod($method, $declaringClass);

        if (true === $implement || null === $implement) {
            return false;
        }

        return true;
    }
}
