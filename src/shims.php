<?php
if ( ! ( class_exists( 'PHPUnit\Runner\Version' ) && version_compare( PHPUnit\Runner\Version::id(), '6.0', '>=' ) ) ) {
	return;
}

/**
 * Load PHPUnit 6.0+ Aliases
 */
$aliases = [
	'PHPUnit\Framework\MockObject\MockObject'                  => 'PHPUnit_Framework_MockObject_MockObject',
	'PHPUnit\Framework\MockObject\Matcher\InvokedRecorder'     => 'PHPUnit_Framework_MockObject_Matcher_InvokedRecorder',
	'PHPUnit\Framework\MockObject\Invocation\ObjectInvocation' => 'PHPUnit_Framework_MockObject_Invocation_Object',
];

foreach ( $aliases as $original => $alias ) {
	if ( ! class_exists( $original ) || class_exists( $alias ) ) {
		continue;
	}
	class_alias( $original, $alias );
}
