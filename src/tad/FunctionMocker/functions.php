<?php

namespace tad\FunctionMocker;

function filterPathListFrom( array $list, $rootDir ) {
	if ( ! ( is_dir( $rootDir ) && is_readable( $rootDir ) ) ) {
		throw new \InvalidArgumentException( $rootDir . ' is not a directory or is not readable.' );
	}

	$_list = array_map( function ( $frag ) use ( $rootDir ) {
		$path = $rootDir . DIRECTORY_SEPARATOR . normalizePathFrag( $frag );

		return file_exists( $path ) ? $path : null;
	}, $list );

	return array_filter( $_list );
}

function normalizePathFrag( $path ) {
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

function findParentContainingFrom( $children, $cwd ) {
	$dir      = $cwd;
	$children = '/' . normalizePathFrag( $children );
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
 * @param array   $userOptions           An array of options as those supported by Patchwork configuration.
 *
 * @return bool Whether the configuration file was written or not.
 *
 * @throws \RuntimeException If the Patchwork configuration file or the checksum file could not be written.
 */
function writePatchworkConfig( array $userOptions ) {
	$destinationFolder = dirname( dirname( dirname( __DIR__) ) );
	$options           = getPatchworkConfiguration( $userOptions, $destinationFolder );

	$configFileContents = json_encode( $options );
	$configChecksum     = md5( $configFileContents );
	$configFilePath     = $destinationFolder . '/patchwork.json';
	$checksumFilePath   = "{$destinationFolder}/pw-cs-{$configChecksum}.yml";

	if ( file_exists( $configFilePath ) && file_exists( $checksumFilePath ) ) {
		return false;
	}

	if ( false === file_put_contents( $configFilePath, $configFileContents ) ) {
		throw new \RuntimeException( "Could not write Patchwork library configuration file to {$configFilePath}" );
	}

	foreach ( glob( $destinationFolder . '/pw-cs-*.yml' ) as $file ) {
		unlink( $file );
	}

	$date                 = date( 'Y-m-d H:i:s' );
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

	// but always exclude function-mocker and Patchwork themselves
	$defaultExcluded      = [ $destinationFolder, getVendorDir( 'antecedent/patchwork' ) ];
	$defaultIncluded      = [ $destinationFolder . '/src/tad/FunctionMocker/utils.php' ];

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

	$options['cache-path'] = rtrim($options['cache-path'], '\\/');

	if ( ! file_exists( $options['cache-path'] ) ) {
		if ( ! mkdir( $options['cache-path'] ) && ! is_dir( $options['cache-path'] ) ) {
			throw new \RuntimeException( sprintf( 'Cache directory "%s" was not created', $options['cache-path'] ) );
		}
		if ( ! file_exists( $options['cache-path'] . '/.gitignore' ) ) {
			file_put_contents( $options['cache-path'] . '/.gitignore', '*' );
		}
	}

	return $options;
}
