<?php

namespace tad\FunctionMocker\CLI;


use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use tad\FunctionMocker\CLI\Exceptions\BreakSignal;
use tad\FunctionMocker\CLI\Exceptions\RuntimeException;
use function tad\FunctionMocker\expandTildeIn;
use function tad\FunctionMocker\findRelativePath;
use function tad\FunctionMocker\getDirsPhpFiles;
use function tad\FunctionMocker\getMaxMemory;
use function tad\FunctionMocker\isInFiles;
use function tad\FunctionMocker\validateFileOrDir;
use function tad\FunctionMocker\validateJsonFile;

class CreateEnv extends Command {

	// @todo update the helper text

	protected $functionIndex = [];
	protected $classIndex = [];
	protected $functionsToFind = [];
	protected $classesToFind = [];
	protected $functionsToFindCount = false;
	protected $classesToFindCount = false;
	protected $startTime = 0;
	protected $bootstrapFile;
	protected $envName;
	protected $source;
	protected $generationConfig = [ 'functions' => [], 'classes' => [] ];
	protected $skipped = [];
	protected $excludedFiles = [];
	protected $sourceFiles = [];
	protected $destination;
	protected $removeDocBlocks = false;
	protected $saveGenerationConfigFile;

	/**
	 * @var \Symfony\Component\Console\Output\Output
	 */
	protected $output;
	/**
	 * @var \Symfony\Component\Console\Input\Input
	 */
	protected $input;
	protected $filesToInclude = [];

	protected function configure() {
		$help = <<< TEXT
This command will parse the source directory or file to find the functions and classes contained within.
The command will copy the code of in the destination directory according to the settings specified in
a <info>configuration file</info>.
If a configuration file is not provided then the command will copy all the functions and/or classes into
the generated environment files.
By default the command will copy the functions and class code as it is, including comments, but its behaviour 
can be configured specifying a <info>configuration JSON file</info> with the `--config` optional argument.
When completed the command will generate a <info>`generation-config.json`</info> file unless the `--save`
option is set to `false`.

When using a JSON configuration file the following optional parameters can be specified:

<info>source</info> - string - the path, absolute or relative to the current working directory, to the source file or directory.
<info>exclude</info> - array - an array of files or folders that should not be parsed from the source
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
	"exclude": [
		"vendor/wordpress/src/wp-admin/includes/noop.php"
	],
	"save": false
}
TEXT;

		$this->setName( 'generate:env' )
		     ->setDescription( 'Generates an environment file from a source folder or file.' )
		     ->setHelp( $help )
		     ->addArgument( 'name', InputArgument::REQUIRED, 'The environment name' )
		     ->addArgument( 'source', InputArgument::IS_ARRAY, 'The environment source files of directories; separate them with a space' )
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
		     ->addOption( 'save', null, InputOption::VALUE_OPTIONAL,
			     'If set to `true` a `generation-config.json` file will be generated in the current working directory.', true );
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface   $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 *
	 * @return int|null|void
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {
		$this->input = $input;
		$this->output = $output;

		$this->checkPhpVersion();
		$this->startExecutionTimer();
		$this->initRunConfiguration();
		$this->readSourceFiles();
		$this->parseSourceFilesForFilesAndFunctions();
		$this->createDestinationDirectory();
		$this->writeFunctionFile();
		$this->writeClassFiles();
		$this->writeEnvBootstrapFile();

		if ( $this->saveGenerationConfigFile ) {
			$this->writeGenerationConfigJsonFile();
		}
	}

	protected function checkPhpVersion() {
		if ( PHP_VERSION_ID < 70000 ) {
			throw RuntimeException::becauseMinimumRequiredPHPVersionIsNotMet();
		}
	}

	protected function startExecutionTimer() {
		$this->startTime = microtime( true );
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 */
	protected function initRunConfiguration() {
		$config = $this->generationConfig = $this->initConfig( $this->input );

		$this->envName = $this->input->getArgument( 'name' );
		$this->source = validateFileOrDir( $config['source'], 'Source file or directory' );
		$this->destination = $config['destination'] ?? getcwd() . '/tests/envs/' . $this->envName;
		$this->bootstrapFile = $config['bootstrap'] ?? $this->destination . '/bootstrap.php';
		$this->excludedFiles = empty( $config['exclude'] ) ? [] : $config['exclude'];
		$this->removeDocBlocks = empty( $config['remove-docblocks'] ) ? false : (bool) $config['remove-docblocks'];
		$this->saveGenerationConfigFile = empty( $config['save'] ) ? false : (bool) $config['save'];
		$this->functionsToFind = $config['functions'] ?? [];
		$this->classesToFind = $config['classes'] ?? [];
		$this->functionsToFindCount = \count( $this->functionsToFind );
		$this->classesToFindCount = \count( $this->classesToFind );
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 *
	 * @return array
	 */
	protected function initConfig( InputInterface $input ): array {
		$name = $input->getArgument( 'name' );
		$inputDestination = $input->hasOption( 'destination' ) ? $input->getOption( 'destination' ) : '/tests/envs';

		$cliConfig = [
			'name'        => $name,
			'source'      => $input->getArgument( 'source' ),
			'destination' => $inputDestination,
			'functions'   => $input->hasOption( 'functions' ) && ! empty( $input->getOption( 'functions' ) ) ?
				preg_split( '\\s*,\\s*', $input->getOption( 'functions' ) )
				: null,
			'classes'     => $input->hasOption( 'classes' ) && ! empty( $input->getOption( 'classes' ) ) ?
				preg_split( '\\s*,\\s*', $input->getOption( 'classes' ) )
				: null,
			'save'        => $input->hasOption( 'save' ) ?
				$input->getOption( 'save' )
				: null,
		];

		foreach ( [ 'source', 'destination' ] as $key ) {
			if ( ! isset( $cliConfig[ $key ] ) ) {
				continue;
			}

			$cliConfig[ $key ] = expandTildeIn( $cliConfig[ $key ] );
		}

		$configFile = $input->getOption( 'config-file' );

		$configFileConfig = [
			'remove-doc-blocks' => false,
			'wrap-in-if'        => true,
			'body'              => 'copy',
		];

		if ( $configFile ) {
			$configFile = validateFileOrDir( $configFile, "JSON configuration file" );
			$configFileConfig = validateJsonFile( $configFile );
		}

		$configFileConfig['_readme'] = [
			"This file defines the {$name} testing environment generation rules.",
			'Read more about it at https://github.com/lucatume/function-mocker.',
			'This file was automatically @generated.',
		];

		$config = array_merge( $configFileConfig, array_filter( $cliConfig, function ( $v ) {
			return null !== $v;
		} ) );

		return $config;
	}

	protected function readSourceFiles() {
		$this->output->writeln( '<info>Reading source files...</info>' );
		$this->sourceFiles = getDirsPhpFiles( $this->source );
		$this->output->writeln( '<info>Found ' . \count( $this->sourceFiles ) . ' source files.</info>' );
	}

	protected function parseSourceFilesForFilesAndFunctions() {
		$this->output->writeln( '<info>Parsing each source file; will stop when all required functions and classes are found.</info>' );

		$progressBar = new ProgressBar( $this->output, \count( $this->sourceFiles ) );
		$maxMemory = $this->getMemoryLimit();
		$maxTime = $this->getTimeLimit();
		foreach ( $this->sourceFiles as $file ) {
			$this->checkMemoryUsage( $maxMemory );
			$this->checkTime( $maxTime );

			$progressBar->advance();

			if ( isInFiles( $file, $this->excludedFiles ) ) {
				continue;
			}

			try {
				/** @var \PhpParser\Node\Stmt[] $allStmts */
				$allStmts = $this->getAllFileStmts( $file );

				$stmts = $this->getFunctionAndClassStmts( $allStmts );
				$wrappedStmts = $this->getIfWrapppedFunctionAndClassStmts( $allStmts );

				$this->indexFileStmts( $file, $stmts );
				$this->indexFileStmts( $file, $wrappedStmts );
			} catch ( BreakSignal $signal ) {
				break;
			} catch ( \Exception $e ) {
				$this->skipped[] = $file;
				continue;
			}
		}

		$progressBar->finish();

		$this->functionIndex = array_unique( $this->functionIndex, SORT_REGULAR );
		$this->classIndex = array_unique( $this->classIndex, SORT_REGULAR );
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 *
	 * @return int
	 */
	protected function getMemoryLimit(): int {
		$maxMemory = getMaxMemory();

		if ( $maxMemory <= 0 ) {
			$this->output->writeln( '<error>PHP memory limit is set to -1: this command has the potential of consuming a lot of memory and will auto-limit itself to 128M.</error>' );
			$maxMemory = - 1;
		}

		return $maxMemory;
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 *
	 * @return int
	 */
	protected function getTimeLimit(): int {
		$maxTime = ini_get( 'max_execution_time' );

		if ( $maxTime <= 0 ) {
			$this->output->writeln( '<error>PHP time limit is not set: this command has the potential of running for a lot of time and will auto-limit itself to 60 seconds.</error>' );
			$maxTime = - 1;
		}

		return $maxTime;
	}

	/**
	 * @param $maxMemory
	 */
	protected function checkMemoryUsage( $maxMemory ) {
		$peakMemoryUsage = memory_get_peak_usage();

		if ( $maxMemory > 0 && $peakMemoryUsage > .9 * $maxMemory ) {
			throw RuntimeException::becauseTheCommandAlmostReachedMemoryLimit();
		}
	}

	/**
	 * @param $maxTime
	 */
	protected function checkTime( $maxTime ) {
		$runningTime = (int) ( microtime( true ) - $this->startTime );
		if ( $maxTime > 0 && $runningTime >= .9 * $maxTime ) {
			throw RuntimeException::becauseTheCommandAlmostReachedTimeLimit();
		}
	}

	protected function getAllFileStmts( $file ) {
		$files = (array) $file;
		$parser = ( new ParserFactory )->create( ParserFactory::PREFER_PHP5 );

		$allStmts = array_map( function ( $file ) use ( $parser ) {
			return $parser->parse( file_get_contents( $file ) );
		}, $files );

		return array_merge( ...$allStmts );
	}

	/**
	 * @param Stmt[] $allStmts
	 *
	 * @return array
	 */
	protected function getFunctionAndClassStmts( array $allStmts ): array {
		$stmts = array_filter( $allStmts, function ( Stmt $stmt ) {
			return $stmt instanceof Function_
				|| $stmt instanceof Class_;
		} );

		return $stmts;
	}

	/**
	 * @param Stmt[] $allStmts
	 *
	 * @return array
	 */
	protected function getIfWrapppedFunctionAndClassStmts( array $allStmts ): array {
		$wrappedStmts = array_reduce( $allStmts, function ( array $found, Stmt $stmt ) {
			/** @var \PhpParser\Node\Stmt\If_ $stmt */
			if ( ! $stmt instanceof Stmt\If_ ) {
				return $found;
			}

			$cond = $stmt->cond;

			/** @var BooleanNot $first */
			if ( ! $cond instanceof BooleanNot ) {
				return $found;
			}

			/** @var \PhpParser\Node\Expr $negated */
			$negated = $cond->expr;

			if ( ! $negated instanceof Expr\FuncCall ) {
				return $found;
			}

			/** @var \PhpParser\Node\Name $funcName */
			$funcName = $negated->name;

			$thisName = $funcName->toString();

			if ( ! \in_array( $thisName, [ 'class_exists', 'function_exists' ] ) ) {
				return $found;
			}

			$found[] = $this->getFunctionAndClassStmts( $stmt->stmts );

			return $found;
		}, [] );

		return empty( $wrappedStmts ) ? [] : array_merge( ...$wrappedStmts );
	}

	protected function indexFileStmts( string $file, array $stmts ) {
		/** @var Stmt $stmt */
		foreach ( $stmts as $stmt ) {
			$name = $stmt->name->name;

			if ( $stmt instanceof Function_ && $this->functionsToFindCount > 0 && ! \in_array( $name, $this->functionsToFind, true ) ) {
				continue;
			}

			if ( $stmt instanceof Class_ && $this->classesToFindCount > 0 && ! \in_array( $name, $this->classesToFind, true ) ) {
				continue;
			}

			$data = [
				'file' => $file,
				'stmt' => $stmt,
			];

			if ( $stmt instanceof Stmt\Function_ ) {
				$this->functionIndex[ $name ] = $data;
				$this->functionsToFindCount --;
			} else {
				$this->classIndex[ $name ] = $data;
				$this->classesToFindCount --;
			}

			if ( 0 === $this->functionsToFindCount && 0 === $this->classesToFindCount ) {
				throw BreawSignal::becauseThereAreNoMoreFunctionsOrClassesToFind();
			}
		}
	}

	/**
	 * @param $destination
	 */
	protected function createDestinationDirectory() {
		if ( ! is_dir( $this->destination ) ) {
			if ( ! mkdir( $this->destination, 0777, true ) && ! is_dir( $this->destination ) ) {
				throw new \RuntimeException( sprintf( 'Could not create destination directory "%s"', $this->destination ) );
			}
		}
	}

	protected function writeFunctionFile() {
		$normalizedFunctionsEntries = $this->normalizeEntries( $this->functionsToFind,
			[ 'removeDocBlocks' => $this->removeDocBlocks, 'body' => 'copy', 'wrapInIf' => true ] );
		$defaultFunctionSettings = (object) [
			'removeDocBlocks' => false,
			'body'            => 'copy',
			'wrapInIf'        => true,
		];

		$functionsFilePath = $this->destination . '/functions.php';
		$functionsFile = $this->openFileForWriting( $functionsFilePath );
		$this->filesToInclude[] = $functionsFilePath;
		$this->writePhpOpeningTagToFile( $functionsFile );
		$this->writeFileHeaderToFile( $functionsFile, "{$this->envName} environment functions" );

		foreach ( $this->functionIndex as $name => $data ) {
			list( $file, $stmt ) = array_values( $data );
			$thisConfig = $normalizedFunctionsEntries[ $name ] ?? $defaultFunctionSettings;
			$generatedConfig = $thisConfig;

			$codePrinter = new Standard;

			$generatedConfig->removeDocBlocks = isset( $thisConfig->removeDocBlocks )
				? (bool) $thisConfig->removeDocBlocks
				: false;
			if ( (bool) $thisConfig->removeDocBlocks ) {
				$stmt->setAttribute( 'comments', [] );
			}

			if ( $thisConfig->body === 'throw' ) {
				$generatedConfig->body = 'throw';
				$stmt->stmts = $this->throwNotImplementedException();
			} elseif ( $thisConfig->body === 'empty' ) {
				$generatedConfig->body = 'empty';
				$stmt->stmts = [];
			} else {
				$generatedConfig->body = 'copy';
			}

			$functionStmt = $stmt;

			$generatedConfig->wrapInIf = isset( $thisConfig->wrapInIf )
				? (bool) $generatedConfig->wrapInIf
				: true;
			if ( (bool) $thisConfig->wrapInIf ) {
				$functionStmt = new Stmt\If_(
					new BooleanNot(
						new Expr\FuncCall(
							new Name( 'function_exists' ),
							[ new Arg( new String_( $name ) ) ]
						)
					),
					[ 'stmts' => [ $stmt ] ]
				);
			}

			$functionCode = $codePrinter->prettyPrint( [ $functionStmt ] ) . "\n\n";
			$generatedConfig->source = findRelativePath( getcwd(), $file );

			fwrite( $functionsFile, $functionCode );
			$this->generationConfig['functions'][ $name ] = $generatedConfig;
		}
		fclose( $functionsFile );
	}

	protected function normalizeEntries( array $entries, array $defaults ): array {
		$normalized = [];
		foreach ( $entries as $index => $entry ) {
			$name = is_numeric( $index ) ? $entry : $index;

			$entry = \is_object( $entry ) ? $entry : new \stdClass();

			foreach ( $defaults as $key => $value ) {
				if ( ! isset( $entry->{$key} ) ) {
					$entry->{$key} = $value;
				}
			}

			$normalized[ $name ] = $entry;
		}

		return $normalized;
	}

	/**
	 * @param $path
	 *
	 * @return bool|resource
	 */
	protected function openFileForWriting( $path ) {
		$functionsFile = fopen( $path, 'wb' );

		return $functionsFile;
	}

	/**
	 * @param $functionsFile
	 */
	protected function writePhpOpeningTagToFile( $functionsFile ) {
		fwrite( $functionsFile, "<?php\n\n" );
	}

	protected function writeFileHeaderToFile( $file, $header ) {
		fwrite( $file, implode( "\n", [
				'/**',
				" * {$header}",
				' *',
				' * @generated by function-mocker environment generation tool on ' . date( 'Y-m-d H:i:s (e)' ),
				' * @link https://github.com/lucatume/function-mocker',
				' */',
			] ) . "\n\n" );
	}

	protected function throwNotImplementedException(): array {
		return [
			new Stmt\Throw_(
				new Expr\New_(
					new Class_( \RuntimeException::class, [
						new Arg( new String_( 'Not implemented.' )
						),
					] )
				)
			),
		];
	}

	protected function writeClassFiles() {
		return;

		$normalizedClassesEntries = $this->normalizeEntries( $this->classesToFind,
			[ 'removeDocBlocks' => $this->removeDocBlocks, 'body' => 'copy', 'wrapInIf' => true ] );
		$defaultClassSetting = [
			'removeDocBlocks' => false,
			'body'            => 'copy',
			'wrapInIf'        => true,
		];
		//
		//		foreach ( $this->classIndex as $name => $classEntry ) {
		//			$slug = slugify( $name );
		//			$file = $destination . '/classes/class-' . $slug . '.php';
		//			$thisConfig = $normalizedClassesEntries[ $name ] ?? $defaultClassSetting;
		//			$classCode = '';
		//
		//			$codePrinter = new Standard;
		//
		//			if ( (bool) $thisConfig->removeDocBlocks ) {
		//				$classEntry->stmt->setAttribute( 'comments', [] );
		//			}
		//
		//			$classCode = "\n" . $codePrinter->prettyPrintFile( [ $classEntry->stmt ] );
		//
		//			if ( ! is_dir( \dirname( $file ) ) ) {
		//				if ( ! mkdir( \dirname( $file ), 0777, true ) && ! is_dir( \dirname( $file ) ) ) {
		//					throw new \RuntimeException( sprintf( 'Directory "%s" was not created', \dirname( $file ) ) );
		//				}
		//			}
		//
		//			file_put_contents( $file, $classCode, LOCK_EX );
		//		}

	}

	protected function writeEnvBootstrapFile() {
		$requireLines = $this->compileIncludePaths( $this->bootstrapFile, $this->filesToInclude );
		$bootstrapCode = "<?php\n\n" . implode( "\n", array_merge( [
				'/**',
				" * {$this->envName} environment bootstrap file",
				' *',
				' * @generated by function-mocker environment generation tool on ' . date( 'Y-m-d H:i:s (e)' ),
				' * @link https://github.com/lucatume/function-mocker',
				' */',
				"\n",
			], $requireLines ) );
		file_put_contents( $this->bootstrapFile, $bootstrapCode, LOCK_EX );
	}

	/**
	 * @param $bootstrap
	 * @param $filesToInclude
	 *
	 * @return array
	 */
	protected function compileIncludePaths( $bootstrap, $filesToInclude ): array {
		$requireLines = array_map( function ( $file ) use ( $bootstrap ) {
			$relativePath = findRelativePath( dirname( $bootstrap ), $file );

			return "require_once( __DIR__ . '/{$relativePath}' );";
		}, $filesToInclude );

		return $requireLines;
	}

	protected function writeGenerationConfigJsonFile() {
		unset( $this->generationConfig['save'] );
		$this->generationConfig['timestamp'] = time();
		$this->generationConfig['date'] = date( 'Y-m-d H:i:s (e)', $this->generationConfig['timestamp'] );
		$this->generationConfig['source'] = array_map( function ( $source ) {
			return findRelativePath( getcwd(), $source );
		}, $this->source );
		$this->generationConfig['destination'] = findRelativePath( getcwd(), $this->destination );
		$this->generationConfig['bootstrap'] = findRelativePath( getcwd(), $this->bootstrapFile );
		$orderedGenerationConfig = $this->orderArrayAs( [
			'_readme',
			'timestamp',
			'date',
			'name',
			'source',
			'destination',
			'bootstrap',
			'remove-doc-blocks',
			'wrap-in-if',
			'body',
			'functions',
			'classes',
		], $this->generationConfig );
		$saveConfigPath = getcwd() . "/{$this->envName}-env-generation-config.json";
		file_put_contents( $saveConfigPath, json_encode( $orderedGenerationConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
	}

	protected function orderArrayAs( array $order, array $toOrder ) {
		uksort( $toOrder, function ( $a, $b ) use ( $order ) {
			$posA = array_search( $a, $order, true );
			$posB = array_search( $b, $order, true );

			return $posA - $posB;
		} );

		return $toOrder;
	}
}
