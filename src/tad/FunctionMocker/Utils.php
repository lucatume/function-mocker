<?php

	namespace src\tad\FunctionMocker;


	class Utils {

		public static function normalizePathFrag( $path ) {
			\Arg::_( $path, 'Path' )->is_string();

			return trim( trim( $path ), '/' );
		}

		public static function findParentContainingFrom( $children, $cwd ) {
			$dir = $cwd;
			$children = '/' . self::normalizePathFrag( $children );
			while ( true ) {
				if ( file_exists( $dir . $children ) ) {
					break;
				} else {
					$dir = dirname( $dir );
				}
			}

			return $dir;
		}
	}

