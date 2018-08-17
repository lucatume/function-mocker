<?php

namespace tad\FunctionMocker\CLI;


use tad\FunctionMocker\CLI\Exceptions\RuntimeException;

trait VersionChecker {

	protected function checkPhpVersion( $phpVersion ) {
		if ( PHP_VERSION_ID < $phpVersion ) {
			throw RuntimeException::becauseMinimumRequiredVersionIsNotMet( $this->getName(), $phpVersion );
		}
	}
}