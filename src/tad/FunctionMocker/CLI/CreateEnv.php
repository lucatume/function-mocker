<?php

namespace tad\FunctionMocker\CLI;


use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateEnv extends Command {

	protected function configure() {
		$help = <<< TEXT
This command will parse the source directory or file to find a list of specified functions or classes.
The command will copy the code of specific functions in a `functions.php` file and the code of specific classes
in a `classes` directory in the destination directory.
If not provided a list of functions and/or classes to copy then the command will copy all the functions and/or 
classes into the genereted environment files.
By default the command will copy the functions and class code as it is, including comments, but its behaviour 
can be configured providing a configuration JSON file with the `--config` optional argument.

When using a JSON configuration file the following optional parameters can be specified:

<info>source</info> - string - the path, absolute or relative to the current working directory, to the source file or directory.
<info>blacklist</info> - array - an array of files or folders that should not be parsed from the source
<info>destination</info> - string - the path, absolute or relative to the current working directory, to the destination directory.
<info>functions</info> - array - an array of functions that should be imported in the environment.
<info>classes</info> - array - an array of classes that should be imported in the environment.
<info>remove-docblocks</info> - bool - whether to remove functions, classes and methods DocBlocks in the generated environment or not.
<info>save</info> - bool - if set to true then a `generation-config.json` file will be generated in the current working directory.

An example configuration file to import a list of functions from WordPress code base:

{
	"source": "vendor/wordpress",
	"destination": "tests/envs/wordpress",
	"functions": [
		"wp_list_filter",
		"wp_list_pluck"
	],
	"classes": [
		"WP_List_Util"
	],
	"remove-docblocks": true,
	"blacklist": [
		"vendor/wordpress/src/wp-admin/includes/noop.php"
	],
	"save": false
}
TEXT;

		$this->setName( 'env:create' )
		     ->setDescription( 'Creates an environment file from a source folder or file.' )
		     ->setHelp( $help )
		     ->addOption( 'source', 's', InputOption::VALUE_OPTIONAL, 'The environment source file of directory.' )
		     ->addOption( 'destination', 'd', InputOption::VALUE_OPTIONAL,
			     'The destination directory in which the environment files should be generated.' )
		     ->addOption( 'functions', null, InputOption::VALUE_OPTIONAL,
			     'A comma separated list of fully qualified function names that should be copied in the environment; will override the `functions` config file parameter; defaults to all found functions.',
			     '' )
		     ->addOption( 'classes', null, InputOption::VALUE_OPTIONAL,
			     'A comma separated list of fully qualified class names that should be copied in the environment; will override the `classes` config file parameter; defaults to all found classes.',
			     '' )
		     ->addOption( 'config-file', 'c', InputOption::VALUE_OPTIONAL,
			     'A configuration file that should be used to fine tune the behaviour of the environment generation.', false )
		     ->addOption( 'remove-docblocks', null, InputOption::VALUE_OPTIONAL,
			     'If set then DocBlock comments will be stripped from functions, classes and methods.', false )
		     ->addOption( 'save', null, InputOption::VALUE_OPTIONAL,
			     'If set to `true` a `generation-config.json` file will be generated in the current working directory.', false );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$cliConfig = [
			'source'           => $input->hasOption( 'source' ) ? $input->getOption( 'source' ) : null,
			'destination'      => $input->hasOption( 'destination' ) ? $input->getOption( 'destination' ) : null,
			'functions'        => $input->hasOption( 'functions' ) && ! empty( $input->getOption( 'functions' ) ) ?
				preg_split( '\\s*,\\s*', $input->getOption( 'functions' ) )
				: null,
			'classes'          => $input->hasOption( 'classes' ) && ! empty( $input->getOption( 'classes' ) ) ?
				preg_split( '\\s*,\\s*', $input->getOption( 'classes' ) )
				: null,
			'remove-docblocks' => $input->hasOption( 'remove-docblocks' ) ?
				$input->getOption( 'remove-docblocks' )
				: null,
			'save'             => $input->hasOption( 'save' ) ?
				$input->getOption( 'save' )
				: null,
		];

		$configFileConfig = [];

		$configFile = $input->getOption( 'config-file' );

		if ( $configFile ) {
			$configFile = $this->validateFileOrDir( $configFile, "JSON configuration file" );
			$configFileConfig = $this->validateJsonFileContents( $configFile );
		}

		$config = $generationConfig = array_merge( $configFileConfig, array_filter( $cliConfig, function ( $v ) {
			return null !== $v;
		} ) );


		$source = $this->validateFileOrDir( $config['source'], 'Source file or directory' );
		$destination = $config['destination'];
		$excluded = empty( $config['blacklist'] ) ? [] : $config['blacklist'];
		$removeDocBlocks = empty( $config['remove-docblocks'] ) ? false : (bool) $config['remove-docblocks'];
		$save = empty( $config['save'] ) ? false : (bool) $config['save'];

		if ( $save ) {
			$saveConfigPath = getcwd() . '/generation-config.json';
		}

		$skipped = [];
		$functions = $config['functions'] ?? [];
		$classes = $config['classes'] ?? [];
		$functionIndex = [];
		$classIndex = [];
		$toFindFunctions = count( $functions );
		$toFindClasses = count( $classes );

		$output->writeln( '<info>Reading source files...</info>' );

		$sourceFiles = $this->getDirPHPFiles( $source );
		$sourceFilesCount = count( $sourceFiles );

		$output->writeln( '<info>Found ' . $sourceFilesCount . ' source files.</info>' );
		$output->writeln( '<info>Parsing each source file; will stop when all required functions and classes are found.</info>' );

		$progressBar = new ProgressBar( $output, $sourceFilesCount );

		foreach ( $sourceFiles as $file ) {
			$progressBar->advance();

			if ( $this->isBetweenFiles( $file, $excluded ) ) {
				continue;
			}

			// list the functions and classes in the file
			$parser = ( new ParserFactory )->create( ParserFactory::PREFER_PHP5 );

			try {
				/** @var \PhpParser\Node\Stmt[] $ast */
				$ast = $parser->parse( file_get_contents( $file ) );

				$stmts = array_filter( $ast, function ( Stmt $stmt ) {
					return $stmt instanceof Function_
						|| $stmt instanceof Class_;
				} );

				/** @var Stmt $stmt */
				foreach ( $stmts as $stmt ) {
					$name = $stmt->name->name;

					if ( $stmt instanceof Function_ && count( $functions ) && ! in_array( $name, $functions ) ) {
						continue;
					} elseif ( $stmt instanceof Class_ && count( $classes ) && ! in_array( $name, $classes ) ) {
						continue;
					}

					$data = (object) [
						'file' => $file,
						'stmt' => $stmt,
					];

					if ( $stmt instanceof Stmt\Function_ ) {
						$functionIndex[ $name ] = $data;
						$toFindFunctions --;
					} else {
						$classIndex[ $name ] = $data;
						$toFindClasses --;
					}

					if ( 0 === $toFindFunctions && 0 === $toFindClasses ) {
						break 2;
					}
				}
			} catch ( \Exception $e ) {
				$skipped[] = $file;
				continue;
			}
		}

		$functionIndex = array_unique( $functionIndex );
		$classIndex = array_unique( $classIndex );

		$generationConfig['functions'] = array_keys( $functionIndex );
		$generationConfig['classes'] = array_keys( $classIndex );

		$progressBar->finish();

		if ( ! is_dir( $destination ) ) {
			if ( ! mkdir( $destination, 0777, true ) && ! is_dir( $destination ) ) {
				throw new \RuntimeException( sprintf( 'Could not create destination directory "%s"', $destination ) );
			}
		}

		$normalizationDefaults = [ 'removeDocBlocks' => $removeDocBlocks, 'body' => 'copy' ];
		$normalizedFunctions = $this->normalizeEntries( $functions, $normalizationDefaults );
		$normalizedClasses = $this->normalizeEntries( $classes, $normalizationDefaults );

		// for each required function add it to the `functions.php` file
		$functionsFile = fopen( $destination . '/functions.php', 'wb' );
		foreach ( $functionIndex as $name => $functionEntry ) {
			$setting = $normalizedFunctions[ $name ];

			$codePrinter = new Standard;

			if ( (bool) $setting->removeDocBlocks ) {
				$functionEntry->stmt->setAttribute( 'comments', [] );
			}

			if ( $setting->body === 'copy' ) {
				$functionCode = "\n" . $codePrinter->prettyPrintFile( [ $functionEntry->stmt ] );
			}

			fwrite( $functionsFile, $functionCode );
		}
		fclose( $functionsFile );

		foreach ( $classIndex as $name => $classEntry ) {
			$slug = $this->slugify( $name );
			$file = $destination . '/classes/' . $slug . '.php';
			$setting = $normalizedClasses[ $name ];

			$codePrinter = new Standard;

			if ( (bool) $setting->removeDocBlocks ) {
				$classEntry->stmt->setAttribute( 'comments', [] );
			}

			if ( $setting->body === 'copy' ) {
				$classCode = "\n" . $codePrinter->prettyPrintFile( [ $classEntry->stmt ] );
			}

			if ( ! is_dir( dirname( $file ) ) ) {
				if ( ! mkdir( dirname( $file ), 0777, true ) && ! is_dir( dirname( $file ) ) ) {
					throw new \RuntimeException( sprintf( 'Directory "%s" was not created', dirname( $file ) ) );
				}
			}

			file_put_contents( $file, $classCode, LOCK_EX );
		}

		if ( $save ) {
			$generationConfig['functions'] = $normalizedFunctions;
			$generationConfig['classes'] = $classes;
			unset( $generationConfig['save'] );
			file_put_contents( $saveConfigPath, json_encode( $generationConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
		}
	}

	protected function validateFileOrDir( string $source, string $name ): string {
		if ( ! file_exists( $source ) ) {
			$source = getcwd() . '/' . trim( $source, '\\/' );
		}

		$source = realpath( $source ) ?: $source;

		if ( ! ( file_exists( $source ) && is_readable( $source ) ) ) {
			throw new InvalidArgumentException( $name . ' [' . $source . '] does not exist or is not readable.' );
		}

		return rtrim( $source, '\\/' );
	}

	protected function validateJsonFileContents( string $file ): array {
		$decoded = json_decode( file_get_contents( $file ), true );

		if ( empty( $decoded ) ) {
			throw new InvalidArgumentException( 'Error while reading [' . $file . ']: ' . json_last_error_msg() );
		}

		return $decoded;
	}

	function getDirPHPFiles( $dir, &$results = [] ) {
		foreach ( scandir( $dir, SCANDIR_SORT_NONE ) as $key => $value ) {
			$path = realpath( $dir . DIRECTORY_SEPARATOR . $value );

			if ( ! is_dir( $path ) ) {
				if ( pathinfo( $path, PATHINFO_EXTENSION ) !== 'php' ) {
					continue;
				}

				$results[] = $path;
			} elseif ( $value !== "." && $value !== ".." ) {
				$this->getDirPHPFiles( $path, $results );
			}
		}

		return $results;
	}

	protected function isBetweenFiles( $needle, array $filesHaystack = array() ) {
		foreach ( $filesHaystack as $file ) {
			if ( strpos( $needle, $file ) === 0 ) {
				return true;
			}
		}

		return false;
	}

	protected function normalizeEntries( $entries, array $defaults ) {
		$normalized = [];
		foreach ( $entries as $index => $entry ) {
			$name = is_numeric( $index ) ? $entry : $index;

			$removeDocBlocks = $defaults['removeDocBlocks'];
			$body = $defaults['body'];

			$entry = is_object( $entry ) ?: (object) [
				'removeDocBlocks' => $removeDocBlocks,
				'body'            => $body,
			];

			if ( ! isset( $entry->removeDocBlocks ) ) {
				$entry->removeDocBlocks = $removeDocBlocks;
			}
			if ( ! isset( $entry->body ) ) {
				$entry->body = $body;
			}

			$normalized[ $name ] = $entry;
		}

		return $normalized;
	}

	protected function slugify( $str ) {
		return strtolower( preg_replace( '/[\\s-_]+/', '-', $str ) );
	}
}
