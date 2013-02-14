<?php
namespace Contrib\Component\Inspector;

/**
 * Inspector for object property.
 *
 * Inspection targets are:
 *
 * * accessibility
 * * name
 * * default value
 *
 * The inspection result:
 *
 * * property name:
 *     * name: string
 *     * modifier: string
 *     optional:
 *     * value: array
 *     * inherit: array
 *     * override: array
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class ObjectPropertyInspector extends ObjectInspector
{
    /**
     * Inspection name.
     *
     * @var string
     */
    protected $inspectionName = 'properties';

    /**
     * ReflectionClass.
     *
     * @var \ReflectionClass
     */
    protected $class;

    /**
     * ReflectionProperty array.
     *
     * @var \ReflectionProperty[]
     */
    protected $properties;

    /**
     * Defaults properties.
     *
     * * key: property name
     * * value: default value
     *
     * @var array
     */
    protected $defaults;

    /**
     * Constructor.
     *
     * @param \ReflectionClass $class   ReflectionClass object.
     * @param array            $options Configuration options.
     */
    public function __construct(\ReflectionClass $class, array $options = array())
    {
        parent::__construct($options);

        $this->class      = $class;
        $this->properties = $class->getProperties();
        $this->defaults   = $class->getDefaultProperties();

        // sort by property name
        usort(
            $this->properties,
            function ($prop1, $prop2) {
                if ($prop1->name === $prop2->name) {
                    return 0;
                }

                return $prop1->name < $prop2->name ? -1 : 1;
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
        $propList = static::sortByModifier($this->properties);

        foreach ($propList as $properties) {
            foreach ($properties as $property) {
                $propName = $property->getName();

                // property name
                $this->inspection[$propName]['name']     = $this->variableIndicator . $propName;
                $this->inspection[$propName]['modifier'] = implode(' ', \Reflection::getModifierNames($property->getModifiers()));

                // default value
                if (isset($this->defaults[$propName])) {
                    $this->inspection[$propName]['value'] = $this->defaults[$propName];
                }

                // property modification defined in the parent class
                if ($this->isInheritProperty($property, $declaringClass)) {
                    $this->inspection[$propName]['inherit'] = $this->getInheritanceInspection($declaringClass);
                } elseif ($this->isOverrideProperty($property, $parentClass)) {
                    $this->inspection[$propName]['override'] = $this->getInheritanceInspection($parentClass);
                }
            }
        }

        $this->inspection = static::sortByInheritance($this->inspection);
    }

    // internal method

    /**
     * Sorts property inspection by modifier order.
     *
     * @param \ReflectionProperty[] $properties Object properties.
     * @return array Sorted properties.
     */
    protected static function sortByModifier(array $properties)
    {
        $sortedByModifier = array();

        foreach ($properties as $property) {
            if ($property instanceof \ReflectionProperty) {
                $sortedByModifier[$property->getModifiers()][] = $property;
            }
        }

        ksort($sortedByModifier);

        return $sortedByModifier;
    }

    /**
     * Sorts inspection by inheritance order.
     *
     * @param array $inspection Inspection.
     * @return array Sorted inspection.
     */
    protected static function sortByInheritance(array $inspection)
    {
        $sortedByInheritance = array();

        foreach ($inspection as $name => $property) {
            if (isset($property['inherit'])) {
                $sortedByInheritance['inherit'][$property['inherit']['name']][$name] = $property;
            } elseif (isset($property['override'])) {
                $sortedByInheritance['override'][$property['override']['name']][$name] = $property;
            } else {
                $sortedByInheritance['declaring'][$name] = $property;
            }
        }

        return $sortedByInheritance;
    }

    /**
     * Returns whether the property is inherited from parent.
     *
     * @param \ReflectionProperty $property        ReflectionProperty object.
     * @param \ReflectionClass    &$declaringClass Output parameter for Declaring class.
     * @return bool true if the property is inherited from parent, false otherwise.
     */
    protected function isInheritProperty(\ReflectionProperty $property, &$declaringClass)
    {
        $className      = $this->class->getName();
        $declaringClass = $property->getDeclaringClass();

        if (false === $declaringClass) {
            return false;
        }

        $declaringClassName = $declaringClass->getName();

        return $className !== $declaringClassName;
    }

    /**
     * Returns whether the propery is overriden from parent.
     *
     * @param \ReflectionProperty $property     ReflectionProperty object.
     * @param \ReflectionClass    &$parentClass Output parameter for parent class.
     * @return bool true if the propery is overriden from parent, false otherwise.
     */
    protected function isOverrideProperty(\ReflectionProperty $property, &$parentClass)
    {
        $parentClass = $this->class->getParentClass();

        if (false === $parentClass) {
            return false;
        }

        if (!$parentClass->hasProperty($property->getName())) {
            return false;
        }

        return true;
    }
}
