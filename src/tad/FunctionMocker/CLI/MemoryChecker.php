<?php

namespace tad\FunctionMocker\CLI;


use tad\FunctionMocker\CLI\Exceptions\RuntimeException;
use function tad\FunctionMocker\getMaxMemory;

class MemoryChecker {

	protected $maxMemory;

	public function __construct() {
		$this->maxMemory = $this->getLimit();
	}

	/**
	 * @return int
	 */
	protected function getLimit() {
		$maxMemory = $this->getMax();

		if ( $maxMemory <= 0 ) {
			$maxMemory = - 1;
		}

		return $maxMemory;
	}

	public function getMax() {
		if ( $this->maxMemory !== null ) {
			return $this->maxMemory;
		}

		try {
			$val = ini_get( 'memory_limit' );
			if ( $val == - 1 ) {
				return - 1;
			}
		} catch ( \Exception $e ) {
			// ok, assume there is no limit
			return - 1;
		}

		$val = trim( $val );

		$last = strtolower( $val[ \strlen( $val ) - 1 ] );
		$val = str_split( $val, \strlen( $val ) - 1 )[0];

		switch ( $last ) {
			case 'g':
				$val *= 1024;
			// go on to MBs
			case 'm':
				$val *= 1024;
			// go on to KBs
			case 'k':
				$val *= 1024;
		}

		$this->maxMemory = $val;

		return $val;
	}

	public function check() {
		$peakMemoryUsage = memory_get_peak_usage();

		if ( $this->maxMemory > 0 && $peakMemoryUsage > .9 * $this->maxMemory ) {
			throw RuntimeException::becauseTheCommandAlmostReachedMemoryLimit();
		}
	}
}