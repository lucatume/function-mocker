<?php
/**
 * General purpose functions used in the project.
 *
 * @package    Function_Mocker
 * @subpackage functions
 */

namespace tad\FunctionMocker;

use InvalidArgumentException;

/**
 * @param array        $list
 * @param string|array $rootDir
 *
 * @return array
 */
function filterPathListFrom( array $list, $rootDir ) {
	if (! ( is_dir($rootDir) && is_readable($rootDir) )) {
		throw new \InvalidArgumentException($rootDir . ' is not a directory or is not readable.');
	}

	$_list = array_map(
		function ( $frag ) use ( $rootDir ) {
			$path = $rootDir . DIRECTORY_SEPARATOR . normalizePath($frag);

			return file_exists($path) ? $path : null;
		},
		$list
	);

	return array_filter($_list);
}

function normalizePath( $path ) {
	return trim(trim($path), '/');
}

function includePatchwork() {
	if (\function_exists('Patchwork\replace')) {
		return;
	}

	/*
	 * @noinspection PhpIncludeInspection
	 */
	include_once getVendorDir('antecedent/patchwork/Patchwork.php');
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
	while ($vendorDir === null) {
		foreach (scandir($root, SCANDIR_SORT_ASCENDING) as $dir) {
			if (is_dir($root . '/' . implode(DIRECTORY_SEPARATOR, [ $dir, 'antecedent', 'patchwork' ]))) {
				$vendorDir = realpath($root . '/' . $dir);
				break;
			}
		}

		$root = \dirname($root);
	}

	return empty($path) ? $vendorDir : $vendorDir . '/' . $path;
}

function findParentContainingFolder( $children, $cwd ) {
	$dir = $cwd;
	$children = '/' . normalizePath($children);
	while (true) {
		if (file_exists($dir . $children)) {
			break;
		}

		$dir = dirname($dir);
	}

	return $dir;
}

/**
 * Writes Patchwork configuration to file if needed.
 *
 * @param array $userOptions An array of options as those supported by Patchwork configuration.
 *
 * @return boolean Whether the configuration file was written or not.
 *
 * @throws \RuntimeException If the Patchwork configuration file or the checksum file could not be written.
 */
function writePatchworkConfig( array $userOptions ) {
	$destinationFolder = dirname(dirname(dirname(__DIR__)));
	$options = getPatchworkConfiguration($destinationFolder, $userOptions);

	$configFileContents = json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	$configChecksum = md5($configFileContents);
	$configFilePath = $destinationFolder . '/patchwork.json';
	$checksumFilePath = "{$destinationFolder}/pw-cs-{$configChecksum}.yml";

	if (file_exists($configFilePath) && file_exists($checksumFilePath)) {
		return false;
	}

	if (false === file_put_contents($configFilePath, $configFileContents)) {
		throw new \RuntimeException("Could not write Patchwork library configuration file to {$configFilePath}");
	}

	$patchworkConfigFiles = new \CallbackFilterIterator(
		new \DirectoryIterator($destinationFolder), static function(\SplFileInfo $file){
			return preg_match('/^pw-cs-.*\.yml$/', $file->getBasename());
		}
	);
	// @var \SplFileInfo $file
	foreach ($patchworkConfigFiles as $file) {
		unlink($file->getPathname());
	}

	$date = date('Y-m-d H:i:s');
	$checksumFileContents = <<< YAML
generator: FunctionMocker
date: $date
checksum: $configChecksum
for: $configFilePath
YAML;

	if (false === file_put_contents($checksumFilePath, $checksumFileContents)) {
		$error = "Could not write Patchwork library configuration checksum file to {$checksumFilePath}";
		throw new \RuntimeException($error);
	}

	return true;
}

/**
 * Returns the Patchwork configuration that should be written to file.
 *
 * @param array  $options           An array of options as those supported by Patchwork configuration.
 * @param string $destinationFolder The absolute path to the folder that will contain the cache folder and the Patchwork
 *                                  configuration file.
 *
 * @return array
 */
function getPatchworkConfiguration( $destinationFolder, array $options = [] ) {
	foreach ([ 'include' => 'whitelist', 'exclude' => 'blacklist' ] as $from => $to) {
		if (! empty($options[ $from ]) && empty($options[ $to ])) {
			$options[ $to ] = $options[ $from ];
		}

		unset($options[ $from ]);
	}

	$destinationFolder = realpath($destinationFolder);

	// but always exclude function-mocker and Patchwork themselves
	$defaultExcluded = [ $destinationFolder, getVendorDir('antecedent/patchwork') ];
	$defaultIncluded = [ $destinationFolder . '/src/tad/FunctionMocker/utils.php' ];

	if (! empty($options['load-wp-env'])) {
		$defaultIncluded[] = $destinationFolder . '/src/includes/wordpress';
	}

	unset($options['load-wp-env']);

	$options['blacklist'] = ! empty($options['blacklist']) ?
		array_merge(array_map('realpath', (array)$options['blacklist']), $defaultExcluded)
		: $defaultExcluded;
	$options['blacklist'] = array_filter($options['blacklist']);

	$options['whitelist'] = ! empty($options['whitelist']) ?
		array_merge(array_map('realpath', (array)$options['whitelist']), $defaultIncluded)
		: $defaultIncluded;
	$options['blacklist'] = array_filter($options['blacklist']);

	if (empty($options['cache-path'])) {
		// by default cache code in a `cache` folder in the package root
		$options['cache-path'] = $destinationFolder . DIRECTORY_SEPARATOR . 'cache';
	}

	$options['cache-path'] = realpath(rtrim($options['cache-path'], '\\/')) ?: $options['cache-path'];

	if (! file_exists($options['cache-path'])) {
		if (! mkdir($options['cache-path'], 0777, true) && ! is_dir($options['cache-path'])) {
			throw new \RuntimeException(sprintf('Cache directory "%s" was not created', $options['cache-path']));
		}
	}

	if (! file_exists($options['cache-path'] . '/.gitignore')) {
		file_put_contents($options['cache-path'] . '/.gitignore', '*');
	}

	$options['cache-path'] = realpath($options['cache-path']);

	return $options;
}

function validatePath( $path ) {
	$original = $path;
	$path = file_exists($path) ? realpath($path) : realpath(getcwd() . '/' . trim($path, '\\/'));

	if (! $path) {
		throw new \InvalidArgumentException("{$original} is not a valid relative or absolute path");
	}

	return $path;
}

function readEnvsFromOptions( array $options ) {
	$envs = isset($options['env']) ? (array)$options['env'] : [ 'WordPress' ];

	if (\in_array('WordPress', $envs, true)) {
		$envs[ array_search('WordPress', $envs, true) ] = __DIR__ . '/envs/WordPress/bootstrap.php';
	}

	return array_map('\tad\FunctionMocker\validatePath', $envs);
}

function whitelistEnvs( array $options, array $envs ) {
	unset($options['env']);

	$options['whitelist'] = isset($options['whitelist']) ? array_merge((array)$options['whitelist'], $envs) : $envs;

	return $options;
}

function includeEnvs( array $envs ) {
	foreach ($envs as $env) {
		if ($env === 'WordPress') {
			include_once __DIR__ . '/envs/WordPress/bootstrap.php';
			continue;
		}

		$realpath = validatePath($env);

		if (is_dir($realpath)) {
			$bootstrap = $realpath . '/bootstrap.php';
			if (! file_exists($bootstrap)) {
				throw UsageException::becauseTheEnvDoesNotSpecifyABootstrapFile($env);
			}

			include_once $bootstrap;
		} else {
			include_once $realpath;
		}
	}
}

function expandTildeIn( $path ) {
	if (! \function_exists('posix_getuid')) {
		return $path;
	}

	$paths = (array)$path;

	foreach ($paths as &$thisPath) {
		if (strpos($thisPath, '~') !== false) {
			$info = posix_getpwuid(posix_getuid());
			$thisPath = str_replace('~', $info['dir'], $thisPath);
		}
	}

	return is_array($path) ? $paths : $paths[0];
}

function validateFileOrDir( $fileOrDir, string $name, $fromRoot = null ) {
	$paths = (array)$fileOrDir;
	$roots = $fromRoot ? array_filter((array)$fromRoot) : [ getcwd() ];

	foreach ($paths as &$path) {
		if (file_exists($path)) {
			continue;
		}

		$found = array_values(
			array_filter(
				array_map(
					function ( $root ) use ( $path ) {
						$path = $root . '/' . trim($path, '\\/');

						return file_exists($path) ? $path : false;
					},
					$roots
				)
			)
		);

		if (\count($found) === 0) {
			throw new InvalidArgumentException($name . ' [' . $path . '] does not exist or is not readable.');
		}

		$path = realpath($found[0]);
	}

	return \is_array($fileOrDir) ? $paths : $paths[0];
}

function validateJsonFile( $file ) {
	$decoded = json_decode(file_get_contents($file), true);

	if (empty($decoded)) {
		throw new InvalidArgumentException('Error while reading [' . $file . ']: ' . json_last_error_msg());
	}

	return $decoded;
}

function isInFiles( $needle, array $filesHaystack = array() ) {
	foreach ($filesHaystack as $file) {
		if (strpos($needle, $file) === 0) {
			return true;
		}
	}

	return false;
}

function getDirsPhpFiles( array $dirs ) {
	$allResults = array_map(__NAMESPACE__ . '\\getDirPhpFiles', $dirs);

	return array_merge(...$allResults);
}

function getDirPhpFiles( $dirOrFile, array &$results = [] ) {
	if (! is_dir($dirOrFile)) {
		$results[] = $dirOrFile;

		return $results;
	}

	$fileInfos = new \CallbackFilterIterator(
		new \DirectoryIterator($dirOrFile),
		static function ( \SplFileInfo $file ) {
			return $file->getExtension() === 'php';
		}
	);
	foreach ($fileInfos as $file) {
		$results[] = $file->getPathname();
	}

	return $results;
}

function strFormat( $str, $replacement ) {
	return preg_replace('/[^\\w]+/', $replacement, $str);
}

function slugify( $str, $replacement = '-' ) {
	return strtolower(strFormat($str, $replacement));
}

function camelCase( $str ) {
	return strFormat(ucwords(strFormat($str, ' ')), '');
}

function prettyLowercase( $string ) {
	return strtolower($result = preg_replace('/[^A-Za-z0-9\\s]/u', ' ', $string));
}

function capitalPDangIt( $string ) {
	$lc = trim(prettyLowercase($string));
	$lc = preg_replace('/wordpress/', 'WordPress', $lc);

	return ucwords($lc);
}

function fullStopIt( $string ) {
	return rtrim($string, '.') . '.';
}

function findRelativePath( $fromPath, $toPath ) {
	$fromPath = realpath($fromPath) ?: $fromPath;
	$toPath = realpath($toPath) ?: $toPath;
	$from = explode(DIRECTORY_SEPARATOR, $fromPath);
	// Folders/File
	$to = explode(DIRECTORY_SEPARATOR, $toPath);
	// Folders/File
	$relpath = '';

	$i = 0;
	// Find how far the path is the same
	while (isset($from[ $i ]) && isset($to[ $i ])) {
		if ($from[ $i ] != $to[ $i ]) {
			break;
		}

		$i ++;
	}

	$j = count($from) - 1;
	// Add '..' until the path is the same
	while ($i <= $j) {
		if (! empty($from[ $j ])) {
			$relpath .= '..' . DIRECTORY_SEPARATOR;
		}

		$j --;
	}

	// Go to folder from where it starts differing
	while (isset($to[ $i ])) {
		if (! empty($to[ $i ])) {
			$relpath .= $to[ $i ] . DIRECTORY_SEPARATOR;
		}

		$i ++;
	}

	// Strip last separator
	return substr($relpath, 0, - 1);
}

function realpath( $file ) {
	return \realpath($file) ?: $file;
}

/**
 * @param array $order
 * @param array $toOrder
 *
 * @return array
 */
function orderAndFilterArray( array $order, array $toOrder ) {
	uksort(
		$toOrder,
		function ( $a, $b ) use ( $order ) {
			$posA = array_search($a, $order, true);
			$posB = array_search($b, $order, true);

			return $posA - $posB;
		}
	);

	return array_intersect_key($toOrder, array_combine($order, $order));
}

/**
 * Returns the OS and config aware path to the temporary directory.
 *
 * Thanks to Drupal code base!
 *
 * @return string The absolute path
 */
function tempDir() {
	static $tempDir;

	if (! empty($tempDir)) {
		return $tempDir;
	}

	$candidates = [];

	// OS specific dirs.
	if (strpos(PHP_OS, 'WIN') === 0) {
		$candidates[] = 'c:\\windows\\temp';
		$candidates[] = 'c:\\winnt\\temp';
	} else {
		$candidates[] = '/tmp';
	}

	// Add the temp directory used by PHP.
	$candidates[] = sys_get_temp_dir();

	// If there is an upload directory available try and use that.
	if (ini_get('upload_tmp_dir')) {
		$candidates[] = ini_get('upload_tmp_dir');
	}

	$available = array_filter(
		$candidates, static function ( $candidate ) {
			return is_dir($candidate) && is_writable($candidate);
		}
	);

	// Fallback on the '/tmp' folder if none available.
	$available = count($available) ? reset($available) : getcwd() . '/tmp';

	// Windows accepts paths with either slash (/) or backslash (\), but will
	// not accept a path which contains both a slash and a backslash. Since
	// the 'file_public_path' variable may have either format, we sanitize
	// everything to use slash which is supported on all platforms.
	$available = str_replace('\\', '/', $available);

	$tempDir = $available;

	if ($tempDir !== '/') {
		$tempDir = rtrim($tempDir, '/');
	}

	return $tempDir;
}
