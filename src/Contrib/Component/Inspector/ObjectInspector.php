<?php
namespace Contrib\Component\Inspector;

/**
 * Object inspector.
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
abstract class ObjectInspector implements ObjectInspectorInterface
{
    /**
     * Expression of parameter passed by reference.
     *
     * @var string
     */
    protected $referenceIndicator = '&';

    /**
     * Expression of variable.
     *
     * @var string
     */
    protected $variableIndicator = '$';

    /**
     * Inspection name.
     *
     * @var string
     */
    protected $inspectionName;

    /**
     * Inspection info.
     *
     * @var array
     */
    protected $inspection = array();

    /**
     * Constructor.
     *
     * @param array $options Configuration options.
     */
    public function __construct(array $options = array())
    {
        $options = $options + static::getDefaultOptions();

        foreach ($options as $name => $option) {
            $this->$name = $option;
        }
    }

    // internal method

    /**
     * Returns inheritance inspection.
     *
     * The inspection result:
     *
     * must:
     *
     * * shortName
     * * name
     * * userDefined
     *
     * optional:
     *
     * * namespace
     *
     * @param \ReflectionClass $declaringClass ReflectionClass object.
     * @return array Inheritance inspection.
     */
    protected function getInheritanceInspection(\ReflectionClass $declaringClass)
    {
        $inheritanceInspection = array(
            'shortname'   => $declaringClass->getShortName(),
            'name'        => $declaringClass->getName(),
            'filename'    => $declaringClass->getFileName(),
            'userDefined' => $declaringClass->isUserDefined(),
        );

        if ($declaringClass->inNamespace()) {
            $inheritanceInspection['namespace'] = $declaringClass->getNamespaceName();
        }

        return $inheritanceInspection;
    }

    // accessor

    /**
     * Returns default options.
     *
     * @return array Default options.
     */
    public static function getDefaultOptions()
    {
        return array(
            'referenceIndicator' => '&',
            'variableIndicator' => '$',
        );
    }

    /**
     * Returns inspection name.
     *
     * @return string Inspection name.
     */
    public function getInspectionName()
    {
        return $this->inspectionName;
    }

    /**
     * Returns inspection info.
     *
     * @return array Inspection info.
     */
    public function getInspection()
    {
        if (empty($this->inspection)) {
            return array();
        }

        return array(
            $this->inspectionName => $this->inspection
        );
    }
}
