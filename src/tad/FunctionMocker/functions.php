<?php

namespace tad\FunctionMocker;

use InvalidArgumentException;

function filterPathListFrom( array $list, $rootDir ) {
	if ( ! ( is_dir( $rootDir ) && is_readable( $rootDir ) ) ) {
		throw new \InvalidArgumentException( $rootDir . ' is not a directory or is not readable.' );
	}

	$_list = array_map( function ( $frag ) use ( $rootDir ) {
		$path = $rootDir . DIRECTORY_SEPARATOR . normalizePath( $frag );

		return file_exists( $path ) ? $path : null;
	}, $list );

	return array_filter( $_list );
}

function normalizePath( $path ) {
	return trim( trim( $path ), '/' );
}

function includePatchwork() {
	if ( function_exists( 'Patchwork\replace' ) ) {
		return;
	}

	/** @noinspection PhpIncludeInspection */
	require_once getVendorDir( 'antecedent/patchwork/Patchwork.php' );
}

/**
 * Gets the absolute path to the `vendor` dir optionally appending a path.
 *
 * @param string $path The relative path with no leading slash.
 *
 * @return string The absolute path to the file.
 */
function getVendorDir( $path = '' ) {
	$vendorDir = null;

	$root = __DIR__;
	while ( $vendorDir === null ) {
		foreach ( scandir( $root, SCANDIR_SORT_ASCENDING ) as $dir ) {
			if ( is_dir( $root . '/' . implode( DIRECTORY_SEPARATOR, [ $dir, 'antecedent', 'patchwork' ] ) ) ) {
				$vendorDir = realpath( $root . '/' . $dir );
				break;
			}
		}
		$root = dirname( $root );
	}

	return empty( $path ) ? $vendorDir : $vendorDir . '/' . $path;
}

function findParentContainingFolder( $children, $cwd ) {
	$dir = $cwd;
	$children = '/' . normalizePath( $children );
	while ( true ) {
		if ( file_exists( $dir . $children ) ) {
			break;
		}

		$dir = dirname( $dir );
	}

	return $dir;
}

/**
 * Writes Patchwork configuration to file if needed.
 *
 *
 * @param array $userOptions An array of options as those supported by Patchwork configuration.
 *
 * @return bool Whether the configuration file was written or not.
 *
 * @throws \RuntimeException If the Patchwork configuration file or the checksum file could not be written.
 */
function writePatchworkConfig( array $userOptions ) {
	$destinationFolder = dirname( dirname( dirname( __DIR__ ) ) );
	$options = getPatchworkConfiguration( $userOptions, $destinationFolder );

	$configFileContents = json_encode( $options );
	$configChecksum = md5( $configFileContents );
	$configFilePath = $destinationFolder . '/patchwork.json';
	$checksumFilePath = "{$destinationFolder}/pw-cs-{$configChecksum}.yml";

	if ( file_exists( $configFilePath ) && file_exists( $checksumFilePath ) ) {
		return false;
	}

	if ( false === file_put_contents( $configFilePath, $configFileContents ) ) {
		throw new \RuntimeException( "Could not write Patchwork library configuration file to {$configFilePath}" );
	}

	foreach ( glob( $destinationFolder . '/pw-cs-*.yml' ) as $file ) {
		unlink( $file );
	}

	$date = date( 'Y-m-d H:i:s' );
	$checksumFileContents = <<< YAML
generator: FunctionMocker
date: $date
checksum: $configChecksum
for: $configFilePath
YAML;

	if ( false === file_put_contents( $checksumFilePath, $checksumFileContents ) ) {
		throw new \RuntimeException( "Could not write Patchwork library configuration checksum file to {$checksumFilePath}" );
	}

	return true;
}

/**
 * Returns the Patchwork configuration that should be written to file.
 *
 * @param array   $options           An array of options as those supported by Patchwork configuration.
 * @param  string $destinationFolder The absolute path to the folder that will contain the cache folder and the Patchwork
 *                                   configuration file.
 *
 * @return array
 */
function getPatchworkConfiguration( array $options = [], $destinationFolder ) {
	foreach ( [ 'include' => 'whitelist', 'exclude' => 'blacklist' ] as $from => $to ) {
		if ( ! empty( $options[ $from ] ) && empty( $options[ $to ] ) ) {
			$options[ $to ] = $options[ $from ];

		}
		unset( $options[ $from ] );
	}

	$destinationFolder = realpath( $destinationFolder );

	// but always exclude function-mocker and Patchwork themselves
	$defaultExcluded = [ $destinationFolder, getVendorDir( 'antecedent/patchwork' ) ];
	$defaultIncluded = [ $destinationFolder . '/src/tad/FunctionMocker/utils.php' ];

	if ( ! empty( $options['load-wp-env'] ) ) {
		$defaultIncluded[] = $destinationFolder . '/src/includes/wordpress';
	}
	unset( $options['load-wp-env'] );

	$options['blacklist'] = ! empty( $options['blacklist'] )
		? array_merge( (array) $options['blacklist'], $defaultExcluded )
		: $defaultExcluded;

	$options['whitelist'] = ! empty( $options['whitelist'] )
		? array_merge( (array) $options['whitelist'], $defaultIncluded )
		: $defaultIncluded;

	if ( empty( $options['cache-path'] ) ) {
		// by default cache code in a `cache` folder in the package root
		$options['cache-path'] = $destinationFolder . DIRECTORY_SEPARATOR . 'cache';
	}

	$options['cache-path'] = realpath( rtrim( $options['cache-path'], '\\/' ) ) ?: $options['cache-path'];

	if ( ! file_exists( $options['cache-path'] ) ) {
		if ( ! mkdir( $options['cache-path'], 0777, true ) && ! is_dir( $options['cache-path'] ) ) {
			throw new \RuntimeException( sprintf( 'Cache directory "%s" was not created', $options['cache-path'] ) );
		}
	}

	if ( ! file_exists( $options['cache-path'] . '/.gitignore' ) ) {
		file_put_contents( $options['cache-path'] . '/.gitignore', '*' );
	}

	$options['cache-path'] = realpath( $options['cache-path'] );

	return $options;
}

function validatePath( $path ) {
	$original = $path;
	$path = file_exists( $path ) ? realpath( $path ) : realpath( getcwd() . '/' . trim( $path, '\\/' ) );

	if ( ! $path ) {
		throw new \InvalidArgumentException( "{$original} is not a valid relative or absolute path" );
	}

	return $path;
}

function readEnvsFromOptions( array $options ) {
	$envs = isset( $options['env'] ) ?
		(array) $options['env']
		// @todo load WordPress env here
		: [];

	return array_map( '\tad\FunctionMocker\validatePath', $envs );
}

function castEnvToDir( $env ) {
	return ! is_dir( $env ) && basename( $env ) === 'env.php' ? dirname( $env ) : $env;
}

function whitelistEnvs( array $options, $envs ) {
	unset( $options['env'] );

	$envs = array_map( '\tad\FunctionMocker\castEnvToDir', $envs );

	$options['whitelist'] = isset( $options['whitelist'] )
		? array_merge( (array) $options['whitelist'], $envs )
		: $envs;

	return $options;
}

function includeEnvs( array $envs ) {
	foreach ( $envs as $env ) {
		$realpath = validatePath( $env );

		if ( is_dir( $realpath ) ) {
			foreach ( glob( $realpath . '/*.php' ) as $file ) {
				require_once $file;
			}
		} else {
			require_once $realpath;
		}
	}
}

function expandTildeIn( $path ) {
	if ( \function_exists( 'posix_getuid' ) && strpos( $path, '~' ) !== false ) {
		$info = posix_getpwuid( posix_getuid() );
		$path = str_replace( '~', $info['dir'], $path );
	}

	return $path;
}

function validateFileOrDir( string $source, string $name ): string {
	if ( ! file_exists( $source ) ) {
		$source = getcwd() . '/' . trim( $source, '\\/' );
	}

	$source = realpath( $source ) ?: $source;

	if ( ! ( file_exists( $source ) && is_readable( $source ) ) ) {
		throw new InvalidArgumentException( $name . ' [' . $source . '] does not exist or is not readable.' );
	}

	return rtrim( $source, '\\/' );
}

function validateJsonFile( string $file ): array {
	$decoded = json_decode( file_get_contents( $file ), true );

	if ( empty( $decoded ) ) {
		throw new InvalidArgumentException( 'Error while reading [' . $file . ']: ' . json_last_error_msg() );
	}

	return $decoded;
}

function getMaxMemory(): int {
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
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}

	return $val;
}

function isInFiles( $needle, array $filesHaystack = array() ) {
	foreach ( $filesHaystack as $file ) {
		if ( strpos( $needle, $file ) === 0 ) {
			return true;
		}
	}

	return false;
}

function getDirPhpFiles( $dir, array &$results = [] ) {
	foreach ( scandir( $dir, SCANDIR_SORT_NONE ) as $key => $value ) {
		$path = realpath( $dir . DIRECTORY_SEPARATOR . $value );

		if ( ! is_dir( $path ) ) {
			if ( pathinfo( $path, PATHINFO_EXTENSION ) !== 'php' ) {
				continue;
			}

			$results[] = $path;
		} elseif ( $value !== '.' && $value !== '..' ) {
			getDirPhpFiles( $path, $results );
		}
	}

	return $results;
}

function slugify( $str ) {
	return strtolower( preg_replace( '/[\\s-_]+/', '-', $str ) );
}
