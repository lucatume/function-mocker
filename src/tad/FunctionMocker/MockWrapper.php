<?php

namespace tad\FunctionMocker;

use tad\FunctionMocker\Template\ClassTemplate;

class MockWrapper
{

    protected $wrappedObject;

    public function wrap($object)
    {
        \Arg::_($object, 'Object to extend')->is_object();
        $this->wrappedObject = $object;
    }

    public function getWrappedObject()
    {
        return $this->wrappedObject;
    }

    public function wrapToSpy(\PHPUnit_Framework_MockObject_MockObject $object ) {
        $mockClassName = get_class($object);
        $extendClassName = 'Extended_' . $mockClassName;

        $classTemplate = new ClassTemplate();
        $classTemplate->setTargetClass($mockClassName);
        $classTemplate->setExtender(new SpyExtender);

        $classTemplate->getClassTemplate();

        $classCode = preg_replace('/%extendedClassName%/', $extendClassName, $classTemplate);
        $classCode = preg_replace('/%mockClassName%/', $mockClassName, $classCode);
    }
}
