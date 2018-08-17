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
 * @return string The class/function fully qualified name.
 */
function resolveNamespace( Name $name, Namespace_ $namespace = null ) {
	if ( $namespace === null ) {
		return $name->toString();
	}

	if ( $name->isFullyQualified() ) {
		return $name->toString();
	}

	// ['Acme', 'Company', 'Service', 'REST']
	$namespaceFrags = array_filter( explode( '\\', $namespace->name->toString() ) );
	// ['Service', 'REST', 'API']
	$objectFrags = array_filter( explode( '\\', $name->toString() ) );
	$common = array_values( array_intersect( $objectFrags, $namespaceFrags ) );
	if ( \count( $common ) > 0 ) {
		$fullyQualified = implode( '\\',
			\array_slice( $namespaceFrags, 0, array_search( $common[0], $namespaceFrags, true ) + 1 ) );
		$fullyQualified .= '\\' . implode( '\\',
				\array_slice( $objectFrags, array_search( $common[0], $objectFrags, true ) ) );

		return $fullyQualified;
	}

	return implode( '\\', $namespaceFrags ) . '\\' . implode( '\\', $objectFrags );

}

/**
 * @param string|array $file
 *
 * @return \PhpParser\Node\Stmt[]
 */
function getAllFileStmts( $file ) {
	$files = (array) $file;
	$parser = ( new ParserFactory )->create( ParserFactory::PREFER_PHP5 );

	$allStmts = array_map(
		function ( $file ) use ( $parser ) {
			return $parser->parse( file_get_contents( $file ) );
		},
		$files
	);

	return array_merge( ...$allStmts );

}

function wrapStmtInIfBlock( Stmt $stmt, string $checkWhat, string $checkHow ) {
	$functionStmt = new Stmt\If_(
		new BooleanNot(
			new FuncCall(
				new Name( $checkHow ),
				[ new Arg( new String_( $checkWhat ) ) ]
			)
		),
		[ 'stmts' => [ $stmt ] ]
	);

	return $functionStmt;

}

function wrapFunctionInIfBlock( Function_ $stmt, string $functionName, string $namespace = null ) {
	$checkHow = empty( $namespace ) || $namespace === '\\' ? 'function_exists' : '\function_exists';

	return wrapStmtInIfBlock( $stmt, $functionName, $checkHow );

}

/**
 * @param \PhpParser\Node\Stmt $stmt
 * @param string               $fqClassName
 * @param string|null          $namespace
 *
 * @return \PhpParser\Node\Stmt\If_
 */
function wrapClassInIfBlock( Stmt $stmt, string $fqClassName, string $namespace = null ) {
	if ( $stmt instanceof Class_ ) {
		$checkHow = empty( $namespace ) || $namespace === '\\' ? 'class_exists' : '\class_exists';
	} elseif ( $stmt instanceof Trait_ ) {
		$checkHow = empty( $namespace ) || $namespace === '\\' ? 'trait_exists' : '\trait_exists';
	} else {
		$checkHow = empty( $namespace ) || $namespace === '\\' ? 'interface_exists' : '\interface_exists';
	}

	return wrapStmtInIfBlock( $stmt, $fqClassName, $checkHow );

}

/**
 * @param \PhpParser\Node\Stmt $stmt
 */
function openPrivateClassMethods( Stmt $stmt ) {
	if ( ! ( $stmt instanceof Class_ || $stmt instanceof Stmt\Trait_ ) ) {
		return;
	}

	array_walk(
		$stmt->stmts,
		function ( Stmt &$stmt ) {
			if ( $stmt instanceof Stmt\ClassMethod && $stmt->isPrivate() ) {
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
function getFunctionAndClassStmts( array $allStmts ) {
	$stmts = array_filter(
		$allStmts,
		function ( $stmt ) {
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
function getIfWrapppedFunctionAndClassStmts( array $allStmts ): array {
	$wrappedStmts = array_reduce(
		$allStmts,
		function ( array $found, $stmt ) {
			/**
			 * @var \PhpParser\Node\Stmt\If_ $stmt
			 */
			if ( ! $stmt instanceof Stmt\If_ ) {
				return $found;
			}

			$cond = $stmt->cond;

			/**
			 * @var BooleanNot $first
			 */
			if ( ! $cond instanceof BooleanNot ) {
				return $found;
			}

			/**
			 * @var \PhpParser\Node\Expr $negated
			 */
			$negated = $cond->expr;

			if ( ! $negated instanceof Expr\FuncCall ) {
				return $found;
			}

			/**
			 * @var \PhpParser\Node\Name $funcName
			 */
			$funcName = $negated->name;

			$thisName = $funcName->toString();

			if ( ! \in_array(
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

			$found[] = getFunctionAndClassStmts( $stmt->stmts );

			return $found;
		},
		[]
	);

	return empty( $wrappedStmts ) ? [] : array_merge( ...$wrappedStmts );

}

/**
 * @param array $allStmts
 *
 * @return array
 */
function getNamespaceStmts( array $allStmts ) {
	return array_filter(
		$allStmts,
		function ( $stmt ) {
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
function findStmtDependencies( Node $node, Namespace_ $namespace = null, array &$dependencies = [] ): array {
	$thisDependencies = [];
	$thisDependencies[] = parseExtendsDependencies( $node, $namespace );
	$thisDependencies[] = parseImplementsDependencies( $node, $namespace );
	$thisDependencies[] = parseFunctionCallDependencies( $node, $namespace );
	$thisDependencies[] = parseNameDependencies( $node, $namespace );
	$thisDependencies[] = parseFunctionParameterDependencies( $node, $namespace );
	$thisDependencies[] = parseSubNodeDependencies( $node, $namespace );

	$thisDependencies = array_merge( ...$thisDependencies );
	$dependencies = array_merge( $dependencies, $thisDependencies );

	return array_unique( $thisDependencies );
}

function parseExtendsDependencies( Node $node, Namespace_ $namespace = null ): array {
	if ( empty( $node->extends ) ) {
		return [];
	}

	$dependencies = [];

	$classFQN = resolveNamespace( $node->extends, $namespace );

	if ( ! isInternalClass( $classFQN ) ) {
		$dependencies[] = $classFQN;
	}

	return $dependencies;
}

/**
 * @param \PhpParser\Node\Stmt $stmt
 */
function removeFinalFromClass( Stmt $stmt ) {
	if ( ( $stmt instanceof Class_ ) && $stmt->isFinal() ) {
		$stmt->flags -= Class_::MODIFIER_FINAL;
	}
}

/**
 * @param \PhpParser\Node\Stmt $stmt
 */
function removeFinalFromClassMethods( Stmt $stmt ) {
	if ( ! $stmt instanceof Class_ ) {
		return;
	}

	array_walk(
		$stmt->stmts,
		function ( Stmt &$stmt ) {
			if ( $stmt instanceof Stmt\ClassMethod && $stmt->isFinal() ) {
				$stmt->flags -= Class_::MODIFIER_FINAL;
			}
		}
	);
}

/**
 * Extracts a function namespace and name from its fully-qualified name.
 *
 * @param string $function The function fully-qualified name.
 *
 * @return array An array containing the original function input, its namespace and its name.
 */
function extractFunctionAndNamespace( $function ) {
	$function = '\\' . ltrim( $function, '\\' );
	$namespaceFrags = array_filter( explode( '\\', $function ) );
	$function = array_pop( $namespaceFrags );
	$namespace = implode( '\\', $namespaceFrags );
	$functionFQN = $namespace . '\\' . $function;

	if ( $function === ltrim( $functionFQN, '\\' ) ) {
		$functionFQN = $function;
	}

	return array( $function, $namespace, $functionFQN );
}

function isInternalClass( string $classFQN ): bool {
	try {
		$classReflection = new \ReflectionClass( $classFQN );

		return $classReflection->isInternal();
	} catch ( \ReflectionException $e ) {
		return false;
	}
}

function isInternalFunction( string $functionFQN ): bool {
	try {
		$functionReflection = new \ReflectionFunction( $functionFQN );

		return $functionReflection->isInternal();
	} catch ( \ReflectionException $e ) {
		return false;
	}
}

function parseImplementsDependencies( Node $node, Namespace_ $namespace = null ): array {
	if ( empty( $node->implements ) ) {
		return [];
	}

	$dependencies = [];

	$implements = is_array( $node->implements ) ? $node->implements : [ $node->implements ];

	foreach ( $implements as $implemented ) {
		$classFQN = resolveNamespace( $implemented, $namespace );

		if ( isInternalClass( $classFQN ) ) {
			continue;
		}

		$dependencies[] = $classFQN;
	}

	return $dependencies;
}

function parseFunctionCallDependencies( Node $node, Namespace_ $namespace = null ): array {
	if ( ! $node instanceof FuncCall || $node->name instanceof Node\Expr\Variable) {
		return [];
	}

	$functionFQN = resolveNamespace( $node->name, $namespace );

	if ( isInternalFunction( $functionFQN ) ) {
		return [];
	}

	$dependencies = [];

	if ( ! ( $node->name instanceof Name\FullyQualified
			|| $node->name instanceof Name\Relative )
		&& \count( $node->name->parts ) === 1
	) {
		/**
		 * Since we cannot know if this is a call to a global function or not
		 * let's just look for the global version of the function too.
		 * This covers calls to global functions in the context of a namespace
		 * w/o the '\global_function` prefix.
		 */
		$dependencies[] = $node->name->toString();
	}

	$dependencies[] = resolveNamespace( $node->name, $namespace );

	return $dependencies;
}

function parseNameDependencies( Node $node, Namespace_ $namespace = null ): array {
	if ( $node instanceof Name ) {
		if ( isInternal( $node->toString() ) ) {
			return [];
		}

		return [ resolveNamespace( $node, $namespace ) ];
	}

	return [];
}

function parseFunctionParameterDependencies( Node $node, Namespace_ $namespace = null ): array {
	if ( ! ( $node instanceof Function_ || $node instanceof Stmt\ClassMethod ) ) {
		return [];
	}
	$params = array_map(
		function ( Node\Param $param ) use ( $namespace ) {
			return resolveNamespace( $param->type, $namespace );
		},
		array_filter(
			$node->getParams(),
			function ( Node\Param $param ) use ( $namespace ) {
				return $param->type instanceof Name
					&& ! (
						isInternalClass( resolveNamespace( $param->type, $namespace ) )
						|| isInternalFunction( resolveNamespace( $param->type, $namespace ) )
					)
					&& ! \in_array( $param->type->toString(), [ 'array', 'bool', 'int', 'float', 'string' ], true );
			}
		)
	);

	return $params;
}

function parseSubNodeDependencies( Node $node, Namespace_ $namespace = null ): array {
	$subNodeNames = array_diff( $node->getSubNodeNames(), [ 'flags', 'parts', 'byRef' ] );
	if ( empty( $subNodeNames ) ) {
		return [];
	}

	$dependencies = array();

	foreach ( $subNodeNames as $subNodeName ) {
		/**
		 * @var Node $subNode
		 */
		$subNode = $node->{$subNodeName};
		$subNodeList = \is_array( $subNode ) ? $subNode : [ $subNode ];

		foreach ( $subNodeList as $subSubNode ) {
			if ( ! $subSubNode instanceof Node ) {
				continue;
			}

			findStmtDependencies( $subSubNode, $namespace, $dependencies );
		}
	}

	return $dependencies;
}

function isInternal( string $name ): bool {
	$internals = [ 'true', 'false', 'null' ];

	return \in_array( $name, $internals, true ) || isInternalFunction( $name ) || isInternalClass( $name );
}
