<?php

namespace tad\FunctionMocker; \Patchwork\CallRerouting\deployQueue();

/**
 * Creates a null returning function.
 *
 * @param string $functionName
 * @param string $functionNamespace
 *
 * @throws \Exception If the function could not be created.
 */
function createFunction( $functionName
	, $functionNamespace = null ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
	$namespace = $functionNamespace ? " {$functionNamespace};" : '';
	$code      = trim( sprintf( 'namespace %s {function %s(){return null;}}', $namespace, $functionName ) );
	$ok        = eval(\Patchwork\CodeManipulation\transformForEval( $code ));
	if ( $ok === false ) {
		throw new \Exception( "Could not eval code $code for function $functionName" );
	}
}
