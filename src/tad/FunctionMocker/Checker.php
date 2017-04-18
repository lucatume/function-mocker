<?php

namespace tad\FunctionMocker;

use const Patchwork\CallRerouting\INTERNAL_REDEFINITION_NAMESPACE;

class Checker
{

	protected static $systemFunctions;
	protected $functionName;
	protected $isEvalCreated;

	public static function fromName($functionName)
	{
		$instance = new self;
		$instance->isEvalCreated = false;
		$instance->functionName = $functionName;
		$isMethod = preg_match("/^[\\w\\d_\\\\]+::[\\w\\d_]+$/", $functionName);
		if (!$isMethod && !function_exists($functionName)) {
            $functionNamespace = self::hasNamespace($functionName) ?
                self::getNamespaceFrom($functionName)
                : '';
            $namespace = $functionNamespace ? "namespace {$functionNamespace};" : '' ;
			$functionName = self::hasNamespace($functionName) ? self::getFunctionNameFrom($functionName) : $functionName;
			$code = sprintf('%s function %s(){return null;}', $namespace, $functionName);
			$ok = eval($code);
			if ($ok === false) {
				throw new \Exception("Could not eval code $code for function $functionName");
			}
			$instance->isEvalCreated = true;
		}

		return $instance;
	}

	/**
	 * @param $functionName
	 *
	 * @return bool
	 */
	private static function hasNamespace($functionName)
	{
		$namespaceElements = explode('\\', $functionName);
		if (count($namespaceElements) === 1) {
			return false;
		}

		return true;
	}

	/**
	 * @param $functionName
	 *
	 * @return string
	 */
	private static function getNamespaceFrom($functionName)
	{
		$namespaceElements = explode('\\', $functionName);
		array_pop($namespaceElements);

		return implode('\\', $namespaceElements);
	}

	private static function getFunctionNameFrom($functionName)
	{
		$elems = explode('\\', $functionName);

		return array_pop($elems);
	}

	public function getFunctionName()
	{
		return $this->functionName;
	}

	public function isEvalCreated()
	{
		return $this->isEvalCreated;
	}
}
