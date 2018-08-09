<?php

namespace tad\FunctionMocker;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\ParserFactory;

/**
 * @param \PhpParser\Node\Name                 $name
 * @param \PhpParser\Node\Stmt\Namespace_|null $namespace
 *
 * @return string
 */
function resolveNamespace(Name $name, Namespace_ $namespace = null) {
	if ($namespace === null) {
		return $name->toString();
	}
	if ($name->isFullyQualified()) {
		return $name->toString();
	}
	// ['Acme', 'Company', 'Service', 'REST']
	$namespaceFrags = array_filter(explode('\\', $namespace->name->toString()));
	// ['Service', 'REST', 'API']
	$objectFrags = array_filter(explode('\\', $name->toString()));
	$common = array_values(array_intersect($objectFrags, $namespaceFrags));
	if (\count($common) > 0) {
		$fullyQualified = implode('\\', \array_slice($namespaceFrags, 0, array_search($common[0], $namespaceFrags, true) + 1));
		$fullyQualified .= '\\' . implode('\\', \array_slice($objectFrags, array_search($common[0], $objectFrags, true)));

		return $fullyQualified;
	}

	return implode('\\', $namespaceFrags) . '\\' . implode('\\', $objectFrags);
}

/**
 * @param string|array $file
 *
 * @return \PhpParser\Node\Stmt[]
 */
function getAllFileStmts($file) {
	$files = (array) $file;
	$parser = ( new ParserFactory )->create(ParserFactory::PREFER_PHP5);

	$allStmts = array_map(
		function ($file) use ($parser) {
			return $parser->parse(file_get_contents($file));
		},
		$files
	);

	return array_merge(...$allStmts);
}

function wrapStmtInIfBlock(Stmt $stmt, string $checkWhat, string $checkHow) {
	$functionStmt = new Stmt\If_(
		new BooleanNot(
			new FuncCall(
				new Name($checkHow),
				[ new Arg(new String_($checkWhat)) ]
			)
		),
		[ 'stmts' => [ $stmt ] ]
	);

	return $functionStmt;
}

function wrapFunctionInIfBlock(Function_ $stmt, string $functionName, string $namespace = null) {
	$checkHow = empty($namespace) || $namespace === '\\' ?
	'function_exists'
	: '\function_exists';

	return wrapStmtInIfBlock($stmt, $functionName, $checkHow);
}

/**
 * @param \PhpParser\Node\Stmt $stmt
 * @param string               $fqClassName
 * @param string|null          $namespace
 *
 * @return \PhpParser\Node\Stmt\If_
 */
function wrapClassInIfBlock(Stmt $stmt, string $fqClassName, string $namespace = null) {
	if ($stmt instanceof Class_) {
		$checkHow = empty($namespace) || $namespace === '\\' ?
		'class_exists'
		: '\class_exists';
	} elseif ($stmt instanceof Trait_) {
		$checkHow = empty($namespace) || $namespace === '\\' ?
		'trait_exists'
		: '\trait_exists';
	} else {
		$checkHow = empty($namespace) || $namespace === '\\' ?
		'interface_exists'
		: '\interface_exists';
	}

	return wrapStmtInIfBlock($stmt, $fqClassName, $checkHow);
}

/**
 * @param \PhpParser\Node\Stmt $stmt
 */
function openPrivateClassMethods(Stmt $stmt) {
	if (! ( $stmt instanceof Class_ || $stmt instanceof Stmt\Trait_ )) {
		return;
	}

	array_walk(
		$stmt->stmts,
		function (Stmt &$stmt) {
			if ($stmt instanceof Stmt\ClassMethod && $stmt->isPrivate()) {
				$stmt->flags -= Class_::MODIFIER_PRIVATE;
				$stmt->flags += Class_::MODIFIER_PROTECTED;
			}
		}
	);
}

/**
 * @param Stmt[] $allStmts
 *
 * @return array
 */
function getFunctionAndClassStmts(array $allStmts) {
	$stmts = array_filter(
		$allStmts,
		function ($stmt) {
			return $stmt instanceof Function_
			|| $stmt instanceof Class_
			|| $stmt instanceof Interface_
			|| $stmt instanceof Stmt\Trait_;
		}
	);

	return $stmts;
}

/**
 * @param Stmt[] $allStmts
 *
 * @return array
 */
function getIfWrapppedFunctionAndClassStmts(array $allStmts): array {
	$wrappedStmts = array_reduce(
		$allStmts,
		function (array $found, $stmt) {
		/**
		* @var \PhpParser\Node\Stmt\If_ $stmt
		*/
			if (! $stmt instanceof Stmt\If_) {
				return $found;
			}

			$cond = $stmt->cond;

		/**
		* @var BooleanNot $first
		*/
			if (! $cond instanceof BooleanNot) {
				return $found;
			}

		/**
		* @var \PhpParser\Node\Expr $negated
		*/
			$negated = $cond->expr;

			if (! $negated instanceof Expr\FuncCall) {
				return $found;
			}

		/**
		* @var \PhpParser\Node\Name $funcName
		*/
			$funcName = $negated->name;

			$thisName = $funcName->toString();

			if (! \in_array(
				$thisName,
				[
				'class_exists',
				'function_exists',
				'interface_exists',
				'trait_exists',
				]
			)
			) {
				return $found;
			}

			$found[] = getFunctionAndClassStmts($stmt->stmts);

			return $found;
		},
		[]
	);

	return empty($wrappedStmts) ? [] : array_merge(...$wrappedStmts);
}

/**
 * @param array $allStmts
 *
 * @return array
 */
function getNamespaceStmts(array $allStmts) {
	return array_filter(
		$allStmts,
		function ($stmt) {
			return $stmt instanceof Namespace_;
		}
	);
}

/**
 * @param \PhpParser\Node                      $node
 * @param \PhpParser\Node\Stmt\Namespace_|null $namespace
 * @param array                                $dependencies
 *
 * @return array
 */
function findStmtDependencies(Node $node, Namespace_ $namespace = null, array &$dependencies = []) {
	if ($node instanceof Class_
		|| $node instanceof Trait_
		|| $node instanceof Interface_
	) {
		if ($node->extends) {
			$dependencies[] = resolveNamespace($node->extends, $namespace);
		}
		if ($node->implements) {
			$dependencies[] = resolveNamespace($node->implements, $namespace);
		}
	}

	if ($node instanceof FuncCall
	) {
		if (! (       $node->name instanceof Name\FullyQualified
			|| $node->name instanceof Name\Relative      )
			&& \count($node->name->parts) === 1
		) {
			/**
			 * Since we cannot know if this is a call to a global function or not
			 * let's just look for the global version of the function too.
			 * This covers calls to global functions in the context of a namespace
			 * w/o the '\global_function` prefix.
			 */
			$dependencies[] = $node->name->toString();
		}
		$dependencies[] = resolveNamespace($node->name, $namespace);
	}

	if ($node instanceof Name) {
		$dependencies[] = resolveNamespace($node, $namespace);
	}

	if ($node instanceof Function_ || $node instanceof Stmt\ClassMethod) {
		$params = array_map(
			function (Node\Param $param) use ($namespace) {
				return resolveNamespace($param->type, $namespace);
			},
			array_filter(
				$node->getParams(),
				function (Node\Param $param) {
					return $param->type instanceof Name
					&& ! \in_array($param->type->toString(), [ 'array', 'bool', 'int', 'float', 'string' ], true);
				}
			)
		);
		$dependencies = array_merge($dependencies, $params);
	}

	$subNodeNames = array_diff($node->getSubNodeNames(), [ 'flags', 'parts', 'byRef' ]);
	if (! empty($subNodeNames)) {
		foreach ($subNodeNames as $subNodeName) {
			/**
	   * @var Node $subNode
*/
			$subNode = $node->{$subNodeName};
			$subNodeList = is_array($subNode) ? $subNode : [ $subNode ];

			foreach ($subNodeList as $subSubNode) {
				if (! $subSubNode instanceof Node) {
					continue;
				}

				findStmtDependencies($subSubNode, $namespace, $dependencies);
			}
		}
	}

	return $dependencies;
}

/**
 * @param \PhpParser\Node\Stmt $stmt
 */
function removeFinalFromClass(Stmt $stmt) {
	if (( $stmt instanceof Class_ ) && $stmt->isFinal()) {
		$stmt->flags -= Class_::MODIFIER_FINAL;
	}
}

/**
 * @param \PhpParser\Node\Stmt $stmt
 */
function removeFinalFromClassMethods(Stmt $stmt) {
	if (! $stmt instanceof Class_) {
		return;
	}

	array_walk(
		$stmt->stmts,
		function (Stmt &$stmt) {
			if ($stmt instanceof Stmt\ClassMethod && $stmt->isFinal()) {
				$stmt->flags -= Class_::MODIFIER_FINAL;
			}
		}
	);
}
