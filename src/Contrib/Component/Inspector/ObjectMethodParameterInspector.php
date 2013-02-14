<?php
namespace Contrib\Component\Inspector;

/**
 * Inspector for object method parameter.
 *
 * * int
 *     must:
 *     * name
 *     optional:
 *     * type
 *     * value
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class ObjectMethodParameterInspector extends ObjectInspector
{
    /**
     * Inspection name.
     *
     * @var string
     */
    protected $inspectionName = 'parameters';

    /**
     * Method parameters.
     *
     * @var array
     */
    protected $params;

    /**
     * Constructor.
     *
     * @param array $params  Method parameters.
     * @param array $options Configuration options.
     */
    public function __construct(array $params, array $options = array())
    {
        parent::__construct($options);

        $this->params = $params;
    }

    // API

    /**
     * {@inheritdoc}
     *
     * @see \Contrib\Component\Inspector\ObjectInspectorInterface::inspect()
     */
    public function inspect()
    {
        foreach ($this->params as $param) {
            $paramList = array();

            // type
            $type = $this->getParameterType($param);

            if (null !== $type) {
                $paramList['type'] = $type;
            }

            // name
            $paramList['name'] = $this->getParameterName($param);

            // value
            if ($param->isDefaultValueAvailable()) {
                $paramList['value'] = $param->getDefaultValue();
            }

            $this->inspection[] = $paramList;
        }
    }

    // internal method

    /**
     * Returns parameter type.
     *
     * @param \ReflectionParameter $param ReflectionParameter object.
     * @return array|null Array if parameter has its type, null otherwise.
     */
    protected function getParameterType(\ReflectionParameter $param)
    {
        if ($param->isArray()) {
            return array('name' => 'array');
        }

        $classType = $param->getClass();

        if (null !== $classType) {
            return $this->getInheritanceInspection($classType);
        }

        return null;
    }

    /**
     * Returns parameter name.
     *
     * @param \ReflectionParameter $param ReflectionParameter object.
     * @return string Parameter name.
     */
    protected function getParameterName(\ReflectionParameter $param)
    {
        if ($param->isPassedByReference()) {
            return $this->referenceIndicator . $this->variableIndicator . $param->getName();
        }

        return $this->variableIndicator . $param->getName();
    }
}
