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
use function tad\FunctionMocker\getDirPhpFiles;
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

		$this->setName( 'generate:env' )
		     ->setDescription( 'Generates an environment file from a source folder or file.' )
		     ->setHelp( $help )
		     ->addArgument( 'source', InputArgument::REQUIRED, 'The environment source file of directory.' )
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

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$this->checkPhpVersion();

		$this->startExecutionTimer();

		$config = $generationConfig = $this->initConfig( $input );

		$source = validateFileOrDir( $config['source'], 'Source file or directory' );
		$destination = $config['destination'] ?? getcwd() . '/tests/envs/' . basename( $source, '.php' );
		$excluded = empty( $config['blacklist'] ) ? [] : $config['blacklist'];
		$removeDocBlocks = empty( $config['remove-docblocks'] ) ? false : (bool) $config['remove-docblocks'];
		$save = empty( $config['save'] ) ? false : (bool) $config['save'];
		$saveConfigPath = null;

		if ( $save ) {
			$saveConfigPath = getcwd() . '/generation-config.json';
		}

		$skipped = [];
		$this->functionsToFind = $config['functions'] ?? [];
		$this->classesToFind = $config['classes'] ?? [];
		$this->functionsToFindCount = \count( $this->functionsToFind );
		$this->classesToFindCount = \count( $this->classesToFind );

		$maxMemory = $this->getMemoryLimit( $output );
		$maxTime = $this->getTimeLimit( $output );

		$output->writeln( '<info>Reading source files...</info>' );

		$sourceRoot = $source;
		$sourceFiles = getDirPhpFiles( $source );
		$sourceFilesCount = count( $sourceFiles );

		$output->writeln( '<info>Found ' . $sourceFilesCount . ' source files.</info>' );
		$output->writeln( '<info>Parsing each source file; will stop when all required functions and classes are found.</info>' );

		$progressBar = new ProgressBar( $output, $sourceFilesCount );

		foreach ( $sourceFiles as $file ) {
			$this->checkMemoryUsage( $maxMemory );
			$this->checkTime( $maxTime );

			$progressBar->advance();

			if ( isInFiles( $file, $excluded ) ) {
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
				$skipped[] = $file;
				continue;
			}
		}

		$this->functionIndex = array_unique( $this->functionIndex, SORT_REGULAR );
		$this->classIndex = array_unique( $this->classIndex, SORT_REGULAR );

		$generationConfig['functions'] = [];
		$generationConfig['classes'] = [];

		$progressBar->finish();

		$this->createDestinationDirectory( $destination );

		$normalizationDefaults = [ 'removeDocBlocks' => $removeDocBlocks, 'body' => 'copy', 'wrapInIf' => true ];
		$normalizedFunctionsEntries = $this->normalizeEntries( $this->functionsToFind, $normalizationDefaults );
		$normalizedClassesEntries = $this->normalizeEntries( $this->classesToFind, $normalizationDefaults );
		$defaultFunctionSettings = (object) [
			'removeDocBlocks' => false,
			'body'            => 'copy',
			'wrapInIf'        => true,
		];
		$defaultClassSetting = [
			'removeDocBlocks' => false,
			'body'            => 'copy',
			'wrapInIf'        => true,
		];

		$functionsFile = $this->openFileForWriting( $destination );
		$this->writePhpOpeningTagToFile( $functionsFile );

		foreach ( $this->functionIndex as $name => $functionEntry ) {
			$thisConfig = $normalizedFunctionsEntries[ $name ] ?? $defaultFunctionSettings;
			$generatedConfig = $thisConfig;

			$codePrinter = new Standard;

			$generatedConfig->removeDocBlocks = isset( $thisConfig->removeDocBlocks )
				? (bool) $thisConfig->removeDocBlocks
				: false;
			if ( (bool) $thisConfig->removeDocBlocks ) {
				$functionEntry->stmt->setAttribute( 'comments', [] );
			}

			if ( $thisConfig->body === 'throw' ) {
				$generatedConfig->body = 'throw';
				$functionEntry->stmt->stmts = $this->throwNotImplementedException();
			} elseif ( $thisConfig->body === 'empty' ) {
				$generatedConfig->body = 'empty';
				$functionEntry->stmt->stmts = [];
			} else {
				$generatedConfig->body = 'copy';
			}

			$functionStmt = $functionEntry->stmt;

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
					[ 'stmts' => [ $functionEntry->stmt ] ]
				);
			}

			$functionCode = $codePrinter->prettyPrint( [ $functionStmt ] ) . "\n\n";
			$generatedConfig->source = str_replace( $sourceRoot, '', $file );
			$generatedConfig->destination = str_replace( getcwd(), '', $destination );

			fwrite( $functionsFile, $functionCode );
			$generationConfig['functions'][ $name ] = $generatedConfig;
		}
		fclose( $functionsFile );
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

		if ( $save ) {
			unset( $generationConfig['save'] );
			$generationConfig['timestamp'] = time();
			$generationConfig['date'] = date( 'Y-m-d H:i:s (e)', $generationConfig['timestamp'] );
			$generationConfig['source'] = $this->findRelativePath( getcwd(), $source );
			$generationConfig['destination'] = $this->findRelativePath( getcwd(), $destination );
			$orderedGenerationConfig = $this->orderArrayAs( [
				'timestamp',
				'date',
				'source',
				'destination',
				'remove-doc-blocks',
				'wrap-in-if',
				'body',
				'functions',
				'classes',
			], $generationConfig );
			file_put_contents( $saveConfigPath, json_encode( $orderedGenerationConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
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
	 *
	 * @return array
	 */
	protected function initConfig( InputInterface $input ): array {
		$cliConfig = [
			'source'      => $input->getArgument( 'source' ),
			'destination' => $input->hasOption( 'destination' ) ? $input->getOption( 'destination' ) : '/tests/envs',
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

		$config = array_merge( $configFileConfig, array_filter( $cliConfig, function ( $v ) {
			return null !== $v;
		} ) );

		return $config;
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 *
	 * @return int
	 */
	protected function getMemoryLimit( OutputInterface $output ): int {
		$maxMemory = getMaxMemory();

		if ( $maxMemory <= 0 ) {
			$output->writeln( '<error>PHP memory limit is set to -1: this command has the potential of consuming a lot of memory and will auto-limit itself to 128M.</error>' );
			$maxMemory = - 1;
		}

		return $maxMemory;
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 *
	 * @return int
	 */
	protected function getTimeLimit( OutputInterface $output ): int {
		$maxTime = ini_get( 'max_execution_time' );

		if ( $maxTime <= 0 ) {
			$output->writeln( '<error>PHP time limit is not set: this command has the potential of running for a lot of time and will auto-limit itself to 60 seconds.</error>' );
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

	protected function getAllFileStmts( string $file ) {
		$parser = ( new ParserFactory )->create( ParserFactory::PREFER_PHP5 );

		return $parser->parse( file_get_contents( $file ) );
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

			$data = (object) [
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
				throw BreakSignal::becauseThereAreNoMoreFunctionsOrClassesToFind();
			}
		}
	}

	/**
	 * @param $destination
	 */
	protected function createDestinationDirectory( $destination ): void {
		if ( ! is_dir( $destination ) ) {
			if ( ! mkdir( $destination, 0777, true ) && ! is_dir( $destination ) ) {
				throw new \RuntimeException( sprintf( 'Could not create destination directory "%s"', $destination ) );
			}
		}
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
	 * @param $destination
	 *
	 * @return bool|resource
	 */
	protected function openFileForWriting( $destination ) {
		$functionsFile = fopen( $destination . '/functions.php', 'wb' );

		return $functionsFile;
	}

	/**
	 * @param $functionsFile
	 */
	protected function writePhpOpeningTagToFile( $functionsFile ): void {
		fwrite( $functionsFile, "<?php\n\n" );
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

	/**
	 *
	 * Find the relative file system path between two file system paths
	 *
	 * @param  string $frompath Path to start from
	 * @param  string $topath   Path we want to end up in
	 *
	 * @return string             Path leading from $frompath to $topath
	 */
	protected function findRelativePath( $frompath, $topath ) {
		$from = explode( DIRECTORY_SEPARATOR, $frompath ); // Folders/File
		$to = explode( DIRECTORY_SEPARATOR, $topath ); // Folders/File
		$relpath = '';

		$i = 0;
		// Find how far the path is the same
		while ( isset( $from[ $i ] ) && isset( $to[ $i ] ) ) {
			if ( $from[ $i ] != $to[ $i ] ) {
				break;
			}
			$i ++;
		}
		$j = count( $from ) - 1;
		// Add '..' until the path is the same
		while ( $i <= $j ) {
			if ( ! empty( $from[ $j ] ) ) {
				$relpath .= '..' . DIRECTORY_SEPARATOR;
			}
			$j --;
		}
		// Go to folder from where it starts differing
		while ( isset( $to[ $i ] ) ) {
			if ( ! empty( $to[ $i ] ) ) {
				$relpath .= $to[ $i ] . DIRECTORY_SEPARATOR;
			}
			$i ++;
		}

		// Strip last separator
		return substr( $relpath, 0, - 1 );
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
