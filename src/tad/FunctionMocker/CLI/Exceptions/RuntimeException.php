<?php

namespace tad\FunctionMocker\CLI\Exceptions;


class RuntimeException extends \RuntimeException {

	public static function becauseTheCommandAlmostReachedMemoryLimit() {
		return new static( 'The command has consumed almost all the available PHP memory: use more stringent criteria for the source to avoid this.' );
	}

	public static function becauseTheCommandAlmostReachedTimeLimit() {
		return new static( 'The command has almost reached the time limit: use more stringent criteria for the source to avoid this.' );
	}

	public static function becauseMinimumRequiredPHPVersionIsNotMet() {
		return new static( 'While Function Mocker has a minimum PHP requirement of PHP 5.6 this CLI tool requires PHP >=7.0' );
	}

	public static function becasueNoSourcesWereSpecified() {
		return new static( 'You need to specify at lease one source file or folder in the CLI arguments or the configuration file.' );
	}
}