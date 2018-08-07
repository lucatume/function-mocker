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
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
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
use tad\FunctionMocker\Templates\EnvAutoloader;
use function tad\FunctionMocker\expandTildeIn;
use function tad\FunctionMocker\findRelativePath;
use function tad\FunctionMocker\getDirsPhpFiles;
use function tad\FunctionMocker\getMaxMemory;
use function tad\FunctionMocker\isInFiles;
use function tad\FunctionMocker\slugify;
use function tad\FunctionMocker\validateFileOrDir;
use function tad\FunctionMocker\validateJsonFile;

class CreateEnv extends Command {

	// @todo update the helper text

	const NOTHING_TO_FIND = 'nothing-to-find';

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
	protected $generationConfig = [];
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
	protected $writeFileHeaders = true;
	protected $configFileDir;
	protected $findAny = false;
	protected $autoloadClasses = [];
	protected $bodyBehaviour;
	protected $autoload;
	protected $wrapInIf;
	protected $openFunctionFiles = [];

	/**
	 * @param bool $writeFileHeaders
	 *
	 * @return CreateEnv
	 */
	public function _writeFileHeaders( bool $writeFileHeaders ): CreateEnv {
		$this->writeFileHeaders = $writeFileHeaders;

		return $this;
	}

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

When using a JSON configuration file the following optional parameters can be specified; read more in the README.
TEXT;

		$this->setName( 'generate:env' )
		     ->setDescription( 'Generates an environment file from a source folder or file.' )
		     ->setHelp( $help )
		     ->addArgument( 'name', InputArgument::REQUIRED, 'The environment name' )
		     ->addArgument( 'source', InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
			     'The environment source files of directories; separate them with a space' )
		     ->addOption( 'destination', 'd', InputOption::VALUE_OPTIONAL,
			     'The destination directory in which the environment files should be generated.' )
		     ->addOption( 'config', 'c', InputOption::VALUE_OPTIONAL,
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
		$this->parseSourceFilesForFunctionsAndClasses();
		$this->createDestinationDirectory();
		$this->writeFunctionFiles();
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
		$this->source = (array) validateFileOrDir( $config['source'], 'Source file or directory', [ getcwd(), $this->configFileDir ] );
		$this->destination = findRelativePath( getcwd(), $config['destination'] ?? getcwd() . '/tests/envs/' . $this->envName );
		$this->bootstrapFile = ! empty( $config['bootstrap'] )
			? $this->destination . '/' . trim( $config['bootstrap'], '\\/' )
			: $this->destination . '/bootstrap.php';
		$this->excludedFiles = empty( $config['exclude'] ) ? [] : $config['exclude'];
		$this->removeDocBlocks = empty( $config['remove-docblocks'] ) ? false : (bool) $config['remove-docblocks'];
		$this->bodyBehaviour = empty( $config['body'] ) ? 'copy' : $config['body'];
		$this->autoload = empty( $config['autoload'] ) ? true : (bool) $config['autoload'];
		$this->wrapInIf = empty( $config['wrapInIf'] ) ? true : (bool) $config['wrapInIf'];
		$this->saveGenerationConfigFile = empty( $config['save'] ) ? false : (bool) $config['save'];
		$this->functionsToFind = $config['functions'] ?? [];
		$this->classesToFind = $config['classes'] ?? [];
		$this->functionsToFindCount = \count( $this->functionsToFind ) ?: static::NOTHING_TO_FIND;
		$this->classesToFindCount = \count( $this->classesToFind ) ?: static::NOTHING_TO_FIND;
		if ( $this->classesToFindCount === static::NOTHING_TO_FIND && $this->functionsToFindCount === static::NOTHING_TO_FIND ) {
			$this->findAny = true;
		}
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
			'save'        => $input->hasOption( 'save' ) ? $input->getOption( 'save' ) : null,
		];

		foreach ( [ 'source', 'destination' ] as $key ) {
			if ( ! isset( $cliConfig[ $key ] ) ) {
				continue;
			}

			$cliConfig[ $key ] = expandTildeIn( $cliConfig[ $key ] );
		}

		$configFile = $input->getOption( 'config' );

		$configFileConfig = [
			'removeDocBlocks' => false,
			'wrapInIf'        => true,
			'body'            => 'copy',
			'autoload'        => true,
		];

		if ( $configFile ) {
			$configFile = validateFileOrDir( $configFile, "JSON configuration file" );
			$configFileConfig = validateJsonFile( $configFile );
			$this->configFileDir = \tad\FunctionMocker\realpath( \dirname( $configFile ) );
		}

		$configFileConfig['_readme'] = [
			"This file defines the {$name} testing environment generation rules.",
			'Read more about it at https://github.com/lucatume/function-mocker.',
			'This file was automatically @generated.',
		];

		if ( empty( $cliConfig['source'] ) ) {
			unset( $cliConfig['source'] );
		}

		$configFileSources = ! empty( $configFileConfig['source'] ) ?
			(array) $configFileConfig['source']
			: [];
		unset( $configFileConfig['source'] );

		$config = array_merge( $configFileConfig, array_filter( $cliConfig, function ( $v ) {
			return null !== $v;
		} ) );

		$config['source'] = ! empty( $config['source'] ) ?
			array_merge( (array) $config['source'], $configFileSources )
			: $configFileSources;

		if ( empty( $config['source'] ) ) {
			throw RuntimeException::becasueNoSourcesWereSpecified();
		}

		$config['source'] = array_unique( $config['source'] );

		return $config;
	}

	protected function readSourceFiles() {
		$this->output->writeln( '<info>Reading source files...</info>' );
		$this->sourceFiles = getDirsPhpFiles( $this->source );
		$this->output->writeln( '<info>Found ' . \count( $this->sourceFiles ) . ' source files.</info>' );
	}

	protected function parseSourceFilesForFunctionsAndClasses() {
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

				$namespaceStmts = $this->getNamespaceStmts( $allStmts );
				if ( \count( $namespaceStmts ) ) {
					/** @var Stmt $namespaceStmt */
					foreach ( $namespaceStmts as $namespaceStmt ) {
						if ( empty( $namespaceStmt->stmts ) ) {
							continue;
						}
						$thisNamesapceStmts = $this->getFunctionAndClassStmts( $namespaceStmt->stmts );
						$thisNamesapceWrappedStmts = $this->getIfWrapppedFunctionAndClassStmts( $namespaceStmt->stmts );
						$this->indexFileStmts( $file, $thisNamesapceStmts, $namespaceStmt );
						$this->indexFileStmts( $file, $thisNamesapceWrappedStmts, $namespaceStmt );
					}
				}

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
		$stmts = array_filter( $allStmts, function ( $stmt ) {
			return $stmt instanceof Function_
				|| $stmt instanceof Class_
				|| $stmt instanceof Interface_
				|| $stmt instanceof Stmt\Trait_;
		} );

		return $stmts;
	}

	/**
	 * @param Stmt[] $allStmts
	 *
	 * @return array
	 */
	protected function getIfWrapppedFunctionAndClassStmts( array $allStmts ): array {
		$wrappedStmts = array_reduce( $allStmts, function ( array $found, $stmt ) {
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

			if ( ! \in_array( $thisName, [
				'class_exists',
				'function_exists',
				'interface_exists',
				'trait_exists',
			] ) ) {
				return $found;
			}

			$found[] = $this->getFunctionAndClassStmts( $stmt->stmts );

			return $found;
		}, [] );

		return empty( $wrappedStmts ) ? [] : array_merge( ...$wrappedStmts );
	}

	protected function getNamespaceStmts( array $allStmts ) {
		return array_filter( $allStmts, function ( $stmt ) {
			return $stmt instanceof Namespace_;
		} );
	}

	protected function indexFileStmts( string $file, array $stmts, Namespace_ $namespace = null ) {
		/** @var Stmt $stmt */
		foreach ( $stmts as $stmt ) {
			$name = $stmt->name instanceof Name ? $stmt->name->name : $stmt->name;

			if ( $namespace !== null ) {
				$name = $namespace->name . "\\{$name}";
			}

			$data = [
				'file'      => $file,
				'stmt'      => $stmt,
				'namespace' => $namespace,
			];

			if (
				$stmt instanceof Function_
				&& (
					$this->findAny
					|| (
						$this->functionsToFindCount > 0 && \array_key_exists( $name, $this->functionsToFind )
					)
				)
			) {
				$this->functionIndex[ $name ] = $data;
				$this->functionsToFindCount --;
			}

			if (
				(
					$stmt instanceof Class_
					|| $stmt instanceof Trait_
					|| $stmt instanceof Interface_
				) && (
					$this->findAny
					|| ( $this->classesToFindCount > 0 && \array_key_exists( $name, $this->classesToFind ) )
				)
			) {
				$this->classIndex[ $name ] = $data;
				$this->classesToFindCount --;
			}


			if ( $this->functionsToFindCount === 0 && $this->classesToFindCount === 0 ) {
				throw BreakSignal::becauseThereAreNoMoreFunctionsOrClassesToFind();
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

	protected function writeFunctionFiles() {
		if ( empty( $this->functionIndex ) ) {
			return;
		}

		$defaultFunctionSettings = [
			'removeDocBlocks' => $this->removeDocBlocks,
			'body'            => $this->bodyBehaviour,
			'wrapInIf'        => $this->wrapInIf,
		];
		$normalizedFunctionsEntries = $this->normalizeEntries( $this->functionsToFind, $defaultFunctionSettings );

		$codePrinter = new Standard;

		$namespaceOrderedFunctions = array_filter( array_reduce( $this->functionIndex, function ( array $acc, array $fEntry ) {
			$namespace = null === $fEntry['namespace'] ? '\\' : $fEntry['namespace']->name;
			/** @var Function_ $stmt */
			$stmt = $fEntry['stmt'];
			$fName = $stmt->name instanceof Name ? $stmt->name->name : $stmt->name;
			$fIndex = $namespace === '\\' ? $fName : $namespace . '\\' . $fName;
			$namespaceString = \is_string( $namespace ) ? $namespace : $namespace->toString();
			$acc[ $namespaceString ][ $fIndex ] = $fEntry;

			return $acc;
		}, [ '\\' => [] ] ) );

		foreach ( $namespaceOrderedFunctions as $namespace => $fEntries ) {
			foreach ( $fEntries as $name => $data ) {
				list( $file, $stmt ) = array_values( $data );
				$thisConfig = $normalizedFunctionsEntries[ $name ] ?? (object) $defaultFunctionSettings;
				$generatedConfig = $thisConfig;

				$functionsFileBasename = ! empty( $thisConfig->fileName ) ? trim( $thisConfig->fileName ) : 'functions.php';

				$functionsFilePath = $namespace === '\\' ?
					$this->destination . '/' . $functionsFileBasename
					: $this->destination . '/' . str_replace( '\\', '/', $namespace ) . '/' . $functionsFileBasename;
				$this->filesToInclude[] = $functionsFilePath;

				$functionFileDirectory = \dirname( $functionsFilePath );
				if ( ! is_dir( $functionFileDirectory ) ) {
					if ( ! mkdir( $functionFileDirectory ) && ! is_dir( $functionFileDirectory ) ) {
						throw new \RuntimeException( sprintf( 'Directory "%s" was not created', $functionFileDirectory ) );
					}
				}

				if ( ! \in_array( $functionsFilePath, $this->openFunctionFiles, true ) && file_exists( $functionsFilePath ) ) {
					unlink( $functionsFilePath );
				}

				$functionsFile = $this->openFileForWriting( $functionsFilePath );

				if ( ! \in_array( $functionsFilePath, $this->openFunctionFiles, true ) ) {
					$this->writePhpOpeningTagToFile( $functionsFile );
					$this->writeFileHeaderToFile( $functionsFile, "{$this->envName} environment functions" );
					$this->writeNamespaceToFile( $functionsFile, $namespace );
					$this->openFunctionFiles[] = $functionsFilePath;
				}


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
					$functionStmt = $this->wrapFunctionInIfBlock( $stmt, $name, $namespace );
				}

				$functionCode = $codePrinter->prettyPrint( [ $functionStmt ] ) . "\n\n";
				$generatedConfig->source = findRelativePath( $this->destination, $file );

				fwrite( $functionsFile, $functionCode );

				$this->generationConfig['functions'][ $name ] = $generatedConfig;

				fclose( $functionsFile );
			}

		}
	}

	protected function normalizeEntries( array $entries, array $defaults ): array {
		$normalized = [];
		foreach ( $entries as $index => $entry ) {
			$name = is_numeric( $index ) ? $entry : $index;
			$normalizedEntry = array_merge( $defaults, (array) $entry );
			$normalized[ $name ] = (object) $normalizedEntry;
		}

		return $normalized;
	}

	/**
	 * @param $path
	 *
	 * @return bool|resource
	 */
	protected function openFileForWriting( $path ) {
		$functionsFile = fopen( $path, 'ab' );

		return $functionsFile;
	}

	/**
	 * @param $functionsFile
	 */
	protected function writePhpOpeningTagToFile( $functionsFile ) {
		fwrite( $functionsFile, "<?php\n\n" );
	}

	protected function writeFileHeaderToFile( $file, $header ) {
		if ( ! $this->writeFileHeaders ) {
			return;
		}
		fwrite( $file, $this->getFileHeader( $header ) );
	}

	/**
	 * @param $header
	 *
	 * @return string
	 */
	protected function getFileHeader( $header ): string {
		return implode( "\n", [
				'/**',
				" * {$header}",
				' *',
				' * @generated by function-mocker environment generation tool on ' . date( 'Y-m-d H:i:s (e)' ),
				' * @link https://github.com/lucatume/function-mocker',
				' */',
			] ) . "\n\n";
	}

	protected function writeNamespaceToFile( $fileHandle, $namespace ) {
		if ( $namespace === '\\' || empty( $namespace ) ) {
			return;
		}
		fwrite( $fileHandle, "namespace {$namespace};\n\n" );
	}

	protected function throwNotImplementedException(): array {
		return [
			new Stmt\Throw_(
				new Expr\New_( new Name( \RuntimeException::class ), [
					new Arg( new String_( 'Not implemented.' )
					),
				] )
			),
		];
	}

	protected function wrapFunctionInIfBlock( Function_ $stmt, string $functionName, string $namespace = null ): \PhpParser\Node\Stmt\If_ {
		$checkHow = empty( $namespace ) || $namespace === '\\' ?
			'function_exists'
			: '\function_exists';

		return $this->wrapStmtInIfBlock( $stmt, $functionName, $checkHow );
	}

	protected function wrapStmtInIfBlock( Stmt $stmt, string $checkWhat, string $checkHow ): \PhpParser\Node\Stmt\If_ {
		$functionStmt = new Stmt\If_(
			new BooleanNot(
				new Expr\FuncCall(
					new Name( $checkHow ),
					[ new Arg( new String_( $checkWhat ) ) ]
				)
			),
			[ 'stmts' => [ $stmt ] ]
		);

		return $functionStmt;
	}

	protected function writeClassFiles() {
		if ( empty( $this->classIndex ) ) {
			return;
		}

		$defaultClassSetting = [
			'removeDocBlocks' => $this->removeDocBlocks,
			'body'            => $this->bodyBehaviour,
			'wrapInIf'        => $this->wrapInIf,
			'autoload'        => $this->autoload,
		];
		$normalizedClassesEntries = $this->normalizeEntries( $this->classesToFind, $defaultClassSetting );

		$codePrinter = new Standard;

		foreach ( $this->classIndex as $name => $classEntry ) {
			/** @var Stmt $stmt */
			/** @var Namespace_ $namespace */
			list( $file, $stmt, $namespace ) = array_values( $classEntry );
			$classFile = $this->destination . '/' . str_replace( '\\', '/', $name ) . '.php';
			$thisConfig = $normalizedClassesEntries[ $name ] ?? (object) $defaultClassSetting;
			$generatedConfig = $thisConfig;

			if ( empty( $thisConfig->autoload ) ) {
				$this->filesToInclude[] = $classFile;
			} else {
				$this->autoloadClasses[] = $name;
			}

			$generatedConfig->removeDocBlocks = isset( $thisConfig->removeDocBlocks )
				? (bool) $thisConfig->removeDocBlocks
				: false;
			if ( (bool) $thisConfig->removeDocBlocks ) {
				$stmt->setAttribute( 'comments', [] );
				array_walk( $stmt->stmts, function ( Stmt &$stmt ) {
					$stmt->setAttribute( 'comments', [] );
				} );
			}

			$this->removeFinalFromClass( $stmt );
			$this->removeFinalFromClassMethods( $stmt );
			$this->openPrivateClassMethods( $stmt );

			if ( $thisConfig->body === 'throw' ) {
				$generatedConfig->body = 'throw';
				array_walk( $stmt->stmts, function ( Stmt &$stmt ) {
					if ( $stmt instanceof Stmt\ClassMethod ) {
						$stmt->stmts = $this->throwNotImplementedException();
					}
				} );
			} elseif ( $thisConfig->body === 'empty' ) {
				$generatedConfig->body = 'empty';
				array_walk( $stmt->stmts, function ( Stmt &$stmt ) {
					if ( $stmt instanceof Stmt\ClassMethod ) {
						$stmt->stmts = [];
					}
				} );
			} else {
				$generatedConfig->body = 'copy';
			}

			$generatedConfig->wrapInIf = isset( $thisConfig->wrapInIf )
				? (bool) $generatedConfig->wrapInIf
				: true;
			if ( (bool) $thisConfig->wrapInIf ) {
				$namespaceString = $namespace instanceof Namespace_ ? $namespace->name : null;
				$stmt = $this->wrapClassInIfBlock( $stmt, $name, $namespaceString );
			}

			$classCode = "\n" . $codePrinter->prettyPrint( [ $stmt ] );

			if ( ! is_dir( \dirname( $classFile ) ) ) {
				if ( ! mkdir( \dirname( $classFile ), 0777, true ) && ! is_dir( \dirname( $classFile ) ) ) {
					throw new \RuntimeException( sprintf( 'Directory "%s" was not created', \dirname( $classFile ) ) );
				}
			}

			$fileHandle = fopen( $classFile, 'wb' );
			$this->writePhpOpeningTagToFile( $fileHandle );
			$this->writeFileHeaderToFile( $fileHandle, "{$name} class." );
			fwrite( $fileHandle, $classCode );
			fclose( $fileHandle );
			$this->generationConfig['classes'][ $name ] = $generatedConfig;
		}
	}

	protected function removeFinalFromClass( Stmt $stmt ) {
		if ( ( $stmt instanceof Class_ ) && $stmt->isFinal() ) {
			$stmt->flags -= Class_::MODIFIER_FINAL;
		}
	}

	protected function removeFinalFromClassMethods( Stmt $stmt ) {
		if ( ! $stmt instanceof Class_ ) {
			return;
		}

		array_walk( $stmt->stmts, function ( Stmt &$stmt ) {
			if ( $stmt instanceof Stmt\ClassMethod && $stmt->isFinal() ) {
				$stmt->flags -= Class_::MODIFIER_FINAL;
			}
		} );
	}

	protected function openPrivateClassMethods( Stmt $stmt ) {
		if ( ! ( $stmt instanceof Class_ || $stmt instanceof Stmt\Trait_ ) ) {
			return;
		}

		array_walk( $stmt->stmts, function ( Stmt &$stmt ) {
			if ( $stmt instanceof Stmt\ClassMethod && $stmt->isPrivate() ) {
				$stmt->flags -= Class_::MODIFIER_PRIVATE;
				$stmt->flags += Class_::MODIFIER_PROTECTED;
			}
		} );
	}

	protected function wrapClassInIfBlock( Stmt $stmt, string $fqClassName, string $namespace = null ): \PhpParser\Node\Stmt\If_ {
		if ( $stmt instanceof Class_ ) {
			$checkHow = empty( $namespace ) || $namespace === '\\' ?
				'class_exists'
				: '\class_exists';
		} elseif ( $stmt instanceof Trait_ ) {
			$checkHow = empty( $namespace ) || $namespace === '\\' ?
				'trait_exists'
				: '\trait_exists';
		} else {
			$checkHow = empty( $namespace ) || $namespace === '\\' ?
				'interface_exists'
				: '\interface_exists';
		}

		return $this->wrapStmtInIfBlock( $stmt, $fqClassName, $checkHow );
	}

	protected function writeEnvBootstrapFile() {
		$requireLines = $this->compileIncludePaths( $this->bootstrapFile, array_unique( $this->filesToInclude ) );
		$bootstrapCode = "<?php\n\n";

		if ( $this->writeFileHeaders ) {
			$headerLines = [
				'/**',
				" * {$this->envName} environment bootstrap file",
				' *',
				' * @generated by function-mocker environment generation tool on ' . date( 'Y-m-d H:i:s (e)' ),
				' * @link https://github.com/lucatume/function-mocker',
				' */',
				"\n",
			];
			$requireLines = array_merge( $headerLines, $requireLines );
		}

		$bootstrapCode .= implode( "\n", $requireLines );
		if ( ! empty( $this->autoloadClasses ) ) {
			$autoloadCode = ( new EnvAutoloader() )->render( [
				'id'       => slugify( $this->envName, '_' ),
				'classMap' => array_combine(
					$this->autoloadClasses,
					array_map( function ( string $class ) {
						return trim( str_replace( '\\', '/', $class ), '/' );
					}, $this->autoloadClasses ) ),
			] );
			$bootstrapCode .= "\n\n" . $autoloadCode;
		}
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

			return "require_once __DIR__ . '/{$relativePath}';";
		}, $filesToInclude );

		return $requireLines;
	}

	protected function writeGenerationConfigJsonFile() {
		unset( $this->generationConfig['save'] );
		if ( $this->writeFileHeaders ) {
			$this->generationConfig['timestamp'] = time();
			$this->generationConfig['date'] = date( 'Y-m-d H:i:s (e)', $this->generationConfig['timestamp'] );
		}
		$this->generationConfig['source'] = array_values( array_unique( array_map( function ( $source ) {
			return findRelativePath( $this->destination, $source );
		}, $this->source ) ) );
		$this->generationConfig['bootstrap'] = findRelativePath( $this->destination, $this->bootstrapFile );
		$orderedGenerationConfig = $this->orderAndFilterArray( [
			'_readme',
			'timestamp',
			'date',
			'name',
			'source',
			'destination',
			'bootstrap',
			'removeDocBlocks',
			'wrapInIf',
			'body',
			'autoload',
			'functions',
			'classes',
		], $this->generationConfig );
		$saveConfigPath = $this->destination . '/generation-config.json';
		file_put_contents( $saveConfigPath, json_encode( $orderedGenerationConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
	}

	protected function orderAndFilterArray( array $order, array $toOrder ) {
		uksort( $toOrder, function ( $a, $b ) use ( $order ) {
			$posA = array_search( $a, $order, true );
			$posB = array_search( $b, $order, true );

			return $posA - $posB;
		} );

		return array_intersect_key( $toOrder, array_combine( $order, $order ) );
	}
}
