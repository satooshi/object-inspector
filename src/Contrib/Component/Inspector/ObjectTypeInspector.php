<?php
namespace Contrib\Component\Inspector;

use Contrib\Component\Annotation\DocCommentLexer;
use Contrib\Component\Annotation\DocCommentParser;
use Contrib\Component\Annotation\AnnotationParser;

/**
 * Inspector for object type.
 *
 * The inspection result:
 *
 * must:
 *
 * * shortname: string
 * * name: string
 * * filename: string
 *
 * optional:
 *
 * * namespace: string
 * * modifier: string
 * * extends: array
 * * implements: array
 * * use: array
 * * doc: array
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class ObjectTypeInspector extends ObjectInspector
{
    /**
     * Inspector name.
     *
     * @var string
     */
    protected $inspectionName = 'class';

    /**
     * ReflectionClass.
     *
     * @var \ReflectionClass
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param \ReflectionClass $class ReflectionClass.
     * @param array $options Configuration options.
     */
    public function __construct(\ReflectionClass $class, array $options = array())
    {
        parent::__construct($options);

        $this->class = $class;
    }

    // API

    /**
     * {@inheritdoc}
     *
     * @see \Contrib\Component\Inspector\ObjectInspectorInterface::inspect()
     */
    public function inspect()
    {
        $this->inspectNamespace();
        $this->inspectModifier();
        $this->inspectClassName();
        $this->inspectParentClass();
        $this->inspectInterface();
        $this->inspectTrait();
        $this->inspectFileName();

        //$this->inspectDocComment();
    }

    protected function inspectNamespace()
    {
        if ($this->class->inNamespace()) {
            $this->inspection['namespace'] = $this->class->getNamespaceName();
        }
    }

    protected function inspectModifier()
    {
        $modifiers = \Reflection::getModifierNames($this->class->getModifiers());

        if (!empty($modifiers)) {
            $this->inspection['modifier'] = implode(' ', $modifiers);
        }
    }

    protected function inspectClassName()
    {
        $this->inspection['shortname'] = $this->class->getShortName();
        $this->inspection['name']      = $this->class->getName();
    }

    protected function inspectParentClass()
    {
        $parentClass = $this->class->getParentClass();

        if (false !== $parentClass) {
            $this->inspection['extends'] = $this->getInheritanceInspection($parentClass);
        }
    }

    protected function inspectInterface()
    {
        $interfaces = $this->class->getInterfaces();

        if (!empty($interfaces)) {
            $isInterface = $this->class->isInterface();

            foreach ($interfaces as $interface) {
                if ($isInterface) {
                    $this->inspection['extends'][] = $this->getInheritanceInspection($interface);
                } else {
                    $this->inspection['implements'][] = $this->getInheritanceInspection($interface);
                }
            }
        }
    }

    protected function inspectTrait()
    {
        if (PHP_VERSION < '5.4') {
            return;
        }

        $traits = $this->class->getTraits();

        if (!empty($traits)) {
            foreach ($traits as $trait) {
                $this->inspection['use'][] = $this->getInheritanceInspection($trait);
            }
        }
    }

    protected function inspectFileName()
    {
        $this->inspection['filename'] = $this->class->getFileName();
    }

    protected function inspectDocComment()
    {
        $doc = $this->class->getDocComment();

        if (false !== $doc) {
            //$lexer = new DocCommentLexer($doc);
            //$lexer->lex($doc);
            //DocCommentParser::lex($doc);
            DocCommentParser::parse($doc);
            $docs = DocCommentParser::parseDocComment($doc);
            $annotations = AnnotationParser::getAnnotations($doc);
            $this->inspection['doc']['shortDescription'] = $docs['shortDescription'];
            $this->inspection['doc']['longDescription'] = implode('<br>', explode("\n", $docs['longDescription']));
            $this->inspection['doc']['annotation'] = $annotations;
        }
    }
}
