<?php
namespace Contrib\Component\Inspector;

/**
 * Inspector for object constant.
 *
 * The inspection result:
 *
 * * constant name
 *     * override: array
 *     * inherit: array
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class ObjectConstantInspector extends ObjectInspector
{
    /**
     * Inspector name.
     *
     * @var string
     */
    protected $inspectionName = 'constants';

    /**
     * ReflectionClass.
     *
     * @var \ReflectionClass
     */
    protected $class;

    /**
     * Constants.
     *
     * @var array
     */
    protected $constants = array();

    /**
     * Constructor.
     *
     * @param \ReflectionClass $class ReflectionClass object.
     * @param array $options Configuration options.
     */
    public function __construct(\ReflectionClass $class, array $options = array())
    {
        parent::__construct($options);

        $this->class = $class;
        $this->constants = $this->class->getConstants();

        // sort by constant name
        ksort($this->constants);
    }

    // API

    /**
     * {@inheritdoc}
     *
     * @see \Contrib\Component\Inspector\ObjectInspectorInterface::inspect()
     */
    public function inspect()
    {
        foreach ($this->constants as $name => $value) {
            $this->inspection[$name]['value'] = $value;

            $parentClass = $this->class->getParentClass();

            if (false === $parentClass) {
                continue;
            }

            if ($this->isInheritConstant($parentClass, $name)) {
                if ($this->isOverrideConstant($parentClass, $name, $value)) {
                    $this->inspection[$name]['override'] = $this->getInheritanceInspection($parentClass);
                } else {
                    $this->inspection[$name]['inherit'] = $this->getInheritanceInspection($parentClass);
                }
            }
        }

        $this->inspection = static::sortByInheritance($this->inspection);
    }

    // internal method

    /**
     * Sorts inspection by inheritance order.
     *
     * @param array $inspection Inspection info.
     * @return array Sorted inspection.
     */
    protected static function sortByInheritance(array $inspection)
    {
        $sortedByInheritance = array();

        foreach ($inspection as $name => $constant) {
            if (isset($constant['inherit'])) {
                $sortedByInheritance['inherit'][$constant['inherit']['name']][$name] = $constant;
            } elseif (isset($constant['override'])) {
                $sortedByInheritance['override'][$constant['override']['name']][$name] = $constant;
            } else {
                $sortedByInheritance['declaring'][$name] = $constant;
            }
        }

        return $sortedByInheritance;
    }

    /**
     * Returns whether the constant is inherited.
     *
     * @param \ReflectionClass $parentClass ReflectionClass object.
     * @param string $constantName Constant name.
     * @return bool true if the constant is inherited, false otherwise.
     */
    protected function isInheritConstant(\ReflectionClass $parentClass, $constantName)
    {
        return $parentClass->hasConstant($constantName);
    }

    /**
     * Returns whether the constant is overriden.
     *
     * @param \ReflectionClass $parentClass ReflectionClass object.
     * @param string $constantName Constant name.
     * @param mixed $constantValue Constant value.
     * @return bool true if the constant is overriden, false otherwise.
     */
    protected function isOverrideConstant(\ReflectionClass $parentClass, $constantName, $constantValue)
    {
        return $constantValue !== $parentClass->getConstant($constantName);
    }
}
