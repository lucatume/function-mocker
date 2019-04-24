<?php
/**
 * The main Function Mocker class.
 *
 * @package    FunctionMocker
 * @subpackage CLI
 * @author     Luca Tumedei <luca@theaveragedev.com>
 * @copyright  2018 Luca Tumedei
 */

namespace tad\FunctionMocker\CLI;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use tad\FunctionMocker\CLI\Exceptions\BreakSignal;
use tad\FunctionMocker\Templates\EnvAutoloader;
use function tad\FunctionMocker\camelCase;
use function tad\FunctionMocker\capitalPDangIt;
use function tad\FunctionMocker\findRelativePath;
use function tad\FunctionMocker\findStmtDependencies;
use function tad\FunctionMocker\fullStopIt;
use function tad\FunctionMocker\getAllFileStmts;
use function tad\FunctionMocker\getDirsPhpFiles;
use function tad\FunctionMocker\getFunctionAndClassStmts;
use function tad\FunctionMocker\getIfWrapppedFunctionAndClassStmts;
use function tad\FunctionMocker\getNamespaceStmts;
use function tad\FunctionMocker\isInFiles;
use function tad\FunctionMocker\openPrivateClassMethods;
use function tad\FunctionMocker\orderAndFilterArray;
use function tad\FunctionMocker\prettyLowercase;
use function tad\FunctionMocker\removeFinalFromClass;
use function tad\FunctionMocker\removeFinalFromClassMethods;
use function tad\FunctionMocker\validateFileOrDir;
use function tad\FunctionMocker\wrapClassInIfBlock;
use function tad\FunctionMocker\wrapFunctionInIfBlock;

class CreateEnv extends Command {

	use VersionChecker;

	// @todo update the helper text
	const NOTHING_TO_FIND = 'nothing-to-find';

	protected $functionIndex = [];

	protected $classIndex = [];

	protected $functionsToFind = [];

	protected $classesToFind = [];

	protected $functionsToFindCount = false;

	protected $classesToFindCount = false;

	protected $bootstrapFile;

	protected $envName;

	protected $source;

	/**
	 * @var \tad\FunctionMocker\CLI\RunConfiguration
	 */
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

	protected $findAny = false;

	protected $autoloadClasses = [];

	protected $bodyBehaviour;

	protected $autoload;

	protected $wrapInIf;

	protected $openFunctionFiles = [];

	protected $withDependencies;

	protected $dependencies = [];

	protected $writeDestination = true;

	protected $backup = [];

	/**
	 * @var Standard
	 */
	protected $codePrinter;

	protected $normalizedFunctionsEntries = [];

	protected $defaultFunctionSettings = [];

	protected $defaultClassSetting = [];

	protected $normalizedClassesEntries = [];

	/**
	 * @var \tad\FunctionMocker\CLI\MemoryChecker
	 */
	protected $memory;

	/**
	 * @var \tad\FunctionMocker\CLI\ExecutionTimeChecker
	 */
	protected $timer;

	/**
	 * The path to the environment destination source folder.
	 *
	 * The autoloader class file and bootstrap files will be written to the destination
	 * while the rest of the generated files will be written to a `/src` folder.
	 *
	 * @var string
	 */
	protected $destinationSrc;

	/**
	 * @param boolean $writeFileHeaders
	 *
	 * @return CreateEnv
	 */
	public function _writeFileHeaders($writeFileHeaders) {
		$this->writeFileHeaders = $writeFileHeaders;

		return $this;
	}

	public function _writeDestination($writeDestination) {
		$this->writeDestination = false;
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

		$this->setName('generate:env')
		     ->setDescription('Generates an environment file from a source folder or file.')
		     ->setHelp($help)
		     ->addArgument('name', InputArgument::REQUIRED, 'The environment name')
		     ->addArgument(
			'source',
			InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
			'The environment source files of directories; separate them with a space'
		)->addOption(
			'destination',
			'd',
			InputOption::VALUE_OPTIONAL,
			'The destination directory in which the environment files should be generated.'
		)->addOption(
			'config',
			'c',
			InputOption::VALUE_OPTIONAL,
			'A configuration file that should be used to fine tune the behaviour of the environment generation.',
			false
		)->addOption(
			'save',
			null,
			InputOption::VALUE_OPTIONAL,
			'If set to `true` a `generation-config.json` file will be generated in the current working directory.',
			true
		)->addOption(
			'with-dependencies',
			null,
			InputOption::VALUE_OPTIONAL,
			'If this flag option is set than the command will try to find and pull in code dependencies of the target code automatically searchin the specified sources.',
			false
		)->addOption(
			'author', null, InputOption::VALUE_OPTIONAL,
			'The string that should be use in the @author tags, e.g. "JD <jd@foo.com>"',
			'Luca Tumedei <luca@theaveragedev.com>'
		)->addOption(
			'copyright', null, InputOption::VALUE_OPTIONAL,
			'The string that should be use in the @copyright tags, e.g. "2018 JD"',
			date('Y') . ' Luca Tumedei'
		);
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface   $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 *
	 * @return integer|null|void
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->input = $input;
		$this->output = $output;
		$this->codePrinter = new Standard();

		$this->memory = new MemoryChecker();
		$this->timer = new ExecutionTimeChecker();

		$this->checkPhpVersion(70000);
		$this->timer->start();
		$this->initRunConfiguration();
		$this->readSourceFiles();
		$this->parseSourceFilesForFunctionsAndClasses('Parsing source files for classes and functions...');
		$this->findDependencies();
		$this->cleanFoundStmts();
		$this->createDestinationDirectory();
		$this->writeFunctionFiles();
		$this->writeClassFiles();
		$this->writeEnvBootstrapFile();

		if ($this->saveGenerationConfigFile) {
			$this->writeGenerationConfigJsonFile();
		}
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 */
	protected function initRunConfiguration() {
		$this->generationConfig = RunConfiguration::fromInput($this->input);
		$this->source = (array)validateFileOrDir(
			$this->generationConfig['source'],
			'Source file or directory',
			[getcwd(), isset($this->generationConfig['configFileDir']) ? $this->generationConfig['configFileDir'] : '']
		);
		$this->destination = findRelativePath(
			getcwd(),
			$this->generationConfig['destination'] ?: getcwd() . '/tests/envs/' . $this->generationConfig['name']
		);
		$this->destinationSrc = $this->destination. '/src';
		$this->bootstrapFile = !empty($this->generationConfig['bootstrap']) ? $this->destination . '/' . trim(
			$this->generationConfig['bootstrap'],
			'\\/'
		) : $this->destination . '/bootstrap.php';
		$this->excludedFiles = empty($this->generationConfig['exclude']) ? [] : $this->generationConfig['exclude'];
		$this->removeDocBlocks = empty($this->generationConfig['remove-docblocks']) ? false : (bool)$this->generationConfig['remove-docblocks'];
		$this->bodyBehaviour = empty($this->generationConfig['body']) ? 'copy' : $this->generationConfig['body'];
		$this->autoload = empty($this->generationConfig['autoload']) ? true : (bool)$this->generationConfig['autoload'];
		$this->wrapInIf = empty($this->generationConfig['wrapInIf']) ? true : (bool)$this->generationConfig['wrapInIf'];
		$this->saveGenerationConfigFile = empty($this->generationConfig['save']) ? false : (bool)$this->generationConfig['save'];
		$this->functionsToFind = isset($this->generationConfig['functions']) ? $this->generationConfig['functions'] : [];
		$this->classesToFind = isset($this->generationConfig['classes']) ? $this->generationConfig['classes'] : [];
		$this->functionsToFindCount = \count($this->functionsToFind) ?: static::NOTHING_TO_FIND;
		$this->classesToFindCount = \count($this->classesToFind) ?: static::NOTHING_TO_FIND;
		if ($this->classesToFindCount === static::NOTHING_TO_FIND && $this->functionsToFindCount === static::NOTHING_TO_FIND) {
			$this->findAny = true;
		}

		$this->withDependencies = ! empty($this->generationConfig['with-dependencies']);
	}

	protected function readSourceFiles() {
		$this->output->writeln('<info>Reading source files...</info>');
		$this->sourceFiles = getDirsPhpFiles($this->source);
		$this->output->writeln('<info>Found ' . \count($this->sourceFiles) . ' source files.</info>');
	}

	protected function parseSourceFilesForFunctionsAndClasses($message) {
		$this->output->writeln("\n<info>{$message}</info>");

		$progressBar = new ProgressBar($this->output, \count($this->sourceFiles));

		foreach ($this->sourceFiles as $file) {
			$this->memory->check();
			$this->timer->check();
			$progressBar->advance();

			if (isInFiles($file, $this->excludedFiles)) {
				continue;
			}

			try {
				$this->parseSourceFile($file);
			} catch (BreakSignal $e) {
				break;
			}
		}

		$progressBar->finish();

		$this->dependencies = array_unique(array_filter($this->dependencies));
	}

	protected function parseSourceFile(string $file) {
		try {
			/*
			 * @var \PhpParser\Node\Stmt[] $allStmts
			 */
			$allStmts = getAllFileStmts($file);

			$stmts = getFunctionAndClassStmts($allStmts);
			$wrappedStmts = getIfWrapppedFunctionAndClassStmts($allStmts);

			$namespaceStmts = getNamespaceStmts($allStmts);
			if (\count($namespaceStmts)) {
				/*
				 * @var Stmt $namespaceStmt
				 */
				foreach ($namespaceStmts as $namespaceStmt) {
					if (empty($namespaceStmt->stmts)) {
						continue;
					}

					$thisNamesapceStmts = getFunctionAndClassStmts($namespaceStmt->stmts);
					$thisNamesapceWrappedStmts = getIfWrapppedFunctionAndClassStmts($namespaceStmt->stmts);
					$this->indexFileStmts($file, $thisNamesapceStmts, $namespaceStmt);
					$this->indexFileStmts($file, $thisNamesapceWrappedStmts, $namespaceStmt);
				}
			}

			$this->indexFileStmts($file, $stmts);
			$this->indexFileStmts($file, $wrappedStmts);
		} catch (BreakSignal $signal) {
			throw $signal;
		} catch (\Exception $e) {
			$this->skipped[] = $file;

			return;
		}
	}

	protected function indexFileStmts(string $file, array $stmts, Namespace_ $namespace = null) {
		/*
		 * @var Stmt $stmt
		 */
		foreach ($stmts as $stmt) {
			$name = $stmt->name instanceof Name ? $stmt->name->name : $stmt->name;

			if ($namespace !== null) {
				$name = $namespace->name . "\\{$name}";
			}

			$data = [
				'file' => $file,
				'stmt' => $stmt,
				'namespace' => $namespace,
			];

			if ($stmt instanceof Function_) {
				if (!array_key_exists($name, $this->functionIndex)
					&& ($this->findAny
					|| ($this->functionsToFindCount > 0 && \array_key_exists($name, $this->functionsToFind)))
				) {
					$this->functionIndex[$name] = $data;
					$this->functionsToFindCount--;
					if ($this->withDependencies) {
						$this->addDependenciesFor($stmt, $namespace);
					}
				}
			}

			if ($stmt instanceof Class_
				|| $stmt instanceof Trait_
				|| $stmt instanceof Interface_
			) {
				if (!array_key_exists($name, $this->classIndex)
					&& ($this->findAny
					|| ($this->classesToFindCount > 0 && \array_key_exists($name, $this->classesToFind)))
				) {
					$this->classIndex[$name] = $data;
					$this->classesToFindCount--;
					if ($this->withDependencies) {
						$this->addDependenciesFor($stmt, $namespace);
					}
				}
			}

			if (!$this->withDependencies && $this->functionsToFindCount === 0 && $this->classesToFindCount === 0) {
				throw BreakSignal::becauseThereAreNoMoreFunctionsOrClassesToFind();
			}
		}
	}

	protected function addDependenciesFor(Stmt $stmt, Namespace_ $namespace = null) {
		$found = findStmtDependencies($stmt, $namespace);
		$this->dependencies = array_merge($this->dependencies, $found);
	}

	protected function findDependencies() {
		if (!$this->withDependencies) {
			return;
		}

		if (empty($this->dependencies)) {
			$this->output->writeln('<info>No dependencies found.</info>');
		}

		$this->backupFound();
		$this->classesToFind = array_combine(
			$this->dependencies,
			array_fill(0, count($this->dependencies), [])
		);
		$this->functionsToFind = array_combine(
			$this->dependencies,
			array_fill(0, count($this->dependencies), [])
		);
		$this->classesToFindCount = $this->functionsToFindCount = count($this->dependencies);
		// shallow dependency resolution
		$this->withDependencies = false;
		$this->parseSourceFilesForFunctionsAndClasses('Parsing source files for dependencies...');
		$this->restoreAndMergeFound();
	}

	protected function backupFound() {
		$this->backup['functionsToFind'] = $this->functionsToFind;
		$this->backup['classesToFind'] = $this->classesToFind;
	}

	protected function restoreAndMergeFound() {
		$this->functionsToFind = array_merge($this->backup['functionsToFind'], $this->functionsToFind);
		$this->classesToFind = array_merge($this->backup['classesToFind'], $this->classesToFind);
	}

	protected function cleanFoundStmts() {
		$this->functionIndex = array_unique($this->functionIndex, SORT_REGULAR);
		$this->classIndex = array_unique($this->classIndex, SORT_REGULAR);
	}

	/**
	 * @param $destination
	 */
	protected function createDestinationDirectory() {
		$destinationSrc = $this->destination . '/src';
		if (!is_dir($destinationSrc)) {
			if (!mkdir($destinationSrc, 0777, true) && !is_dir($destinationSrc)) {
				throw new \RuntimeException(
					sprintf(
						'Could not create destination directory "%s"',
						$destinationSrc
					)
				);
			}
		}
	}

	protected function writeFunctionFiles() {
		if (empty($this->functionIndex)) {
			return;
		}

		$this->defaultFunctionSettings = [
			'removeDocBlocks' => $this->removeDocBlocks,
			'body' => $this->bodyBehaviour,
			'wrapInIf' => $this->wrapInIf,
		];
		$this->normalizedFunctionsEntries = $this->normalizeEntries(
			$this->functionsToFind,
			$this->defaultFunctionSettings
		);

		foreach ($this->orderFunctionsByNamespace() as $namespace => $fEntries) {
			foreach ($fEntries as $name => $data) {
				$this->writeFunctionFile($data, $name, $namespace);
			}
		}
	}

	protected function normalizeEntries(array $entries, array $defaults) {
		$normalized = [];
		foreach ($entries as $index => $entry) {
			$name = is_numeric($index) ? $entry : $index;
			$normalizedEntry = array_merge($defaults, (array)$entry);
			$normalized[$name] = (object)$normalizedEntry;
		}

		return $normalized;
	}

	/**
	 * @return array
	 */
	protected function orderFunctionsByNamespace() {
		$namespaceOrderedFunctions = array_filter(
			array_reduce(
				$this->functionIndex,
				function (array $acc, array $fEntry) {
					$namespace = null === $fEntry['namespace'] ? '\\' : $fEntry['namespace']->name;
					/*
					 * @var Function_ $stmt
					 */
					$stmt = $fEntry['stmt'];
					$fName = $stmt->name instanceof Name ? $stmt->name->name : $stmt->name;
					$fIndex = $namespace === '\\' ? $fName : $namespace . '\\' . $fName;
					$namespaceString = \is_string($namespace) ? $namespace : $namespace->toString();
					$acc[$namespaceString][$fIndex] = $fEntry;

					return $acc;
				},
				['\\' => []]
			)
		);

		return $namespaceOrderedFunctions;
	}

	protected function writeFunctionFile($data, $name, $namespace) {
		list($file, $stmt) = array_values($data);
		$thisConfig = isset($this->normalizedFunctionsEntries[ $name ])
			? $this->normalizedFunctionsEntries[ $name ]
			: (object)$this->defaultFunctionSettings;
		$generatedConfig = $thisConfig;

		$functionsFileBasename = !empty($thisConfig->fileName) ? trim($thisConfig->fileName) : 'functions.php';

		$functionsFilePath = $namespace === '\\'
			? $this->destinationSrc . '/' . $functionsFileBasename
			: $this->destinationSrc . '/' . str_replace(
				'\\',
				'/', $namespace
			) . '/' . $functionsFileBasename;
		$this->filesToInclude[] = $functionsFilePath;

		$functionFileDirectory = \dirname($functionsFilePath);
		if (!is_dir($functionFileDirectory)) {
			if (!mkdir($functionFileDirectory) && !is_dir($functionFileDirectory)) {
				throw new \RuntimeException(
					sprintf(
						'Directory "%s" was not created',
						$functionFileDirectory
					)
				);
			}
		}

		if (!\in_array(
			$functionsFilePath, $this->openFunctionFiles,
			true
		) && file_exists($functionsFilePath)
		) {
			unlink($functionsFilePath);
		}

		$functionsFile = $this->openFileForWriting($functionsFilePath);

		if (!\in_array($functionsFilePath, $this->openFunctionFiles, true)) {
			$this->writePhpOpeningTagToFile($functionsFile);
			$this->writeFileHeaderToFile(
				$functionsFile, capitalPDangIt($this->generationConfig['name'])
				. ' environment '
				. prettyLowercase(basename($functionsFilePath, '.php'))
			);
			$this->writeNamespaceToFile($functionsFile, $namespace);
			$this->openFunctionFiles[] = $functionsFilePath;
		}

		$generatedConfig->removeDocBlocks = isset($thisConfig->removeDocBlocks) ? (bool)$thisConfig->removeDocBlocks : false;
		if ((bool)$thisConfig->removeDocBlocks) {
			$stmt->setAttribute('comments', []);
		}

		if ($thisConfig->body === 'throw') {
			$generatedConfig->body = 'throw';
			$stmt->stmts = $this->throwNotImplementedException();
		} elseif ($thisConfig->body === 'empty') {
			$generatedConfig->body = 'empty';
			$stmt->stmts = [];
		} else {
			$generatedConfig->body = 'copy';
		}

		$functionStmt = $stmt;

		$generatedConfig->wrapInIf = isset($thisConfig->wrapInIf) ? (bool)$generatedConfig->wrapInIf : true;
		if ((bool)$thisConfig->wrapInIf) {
			$functionStmt = wrapFunctionInIfBlock($stmt, $name, $namespace);
		}

		$functionCode = $this->codePrinter->prettyPrint([$functionStmt]) . "\n\n";
		$generatedConfig->source = findRelativePath($this->destinationSrc, $file);

		fwrite($functionsFile, $functionCode);

		$this->generationConfig->addFunctionConfig(
			$name, orderAndFilterArray(
				[
					'removeDocBlocks',
					'body',
					'wrapInIf',
					'source',
					'fileName',
				], (array)$generatedConfig
			)
		);

		fclose($functionsFile);
	}

	/**
	 * @param $path
	 *
	 * @return boolean|resource
	 */
	protected function openFileForWriting($path) {
		$functionsFile = fopen($path, 'ab');

		return $functionsFile;
	}

	/**
	 * @param $functionsFile
	 */
	protected function writePhpOpeningTagToFile($functionsFile) {
		fwrite($functionsFile, "<?php\n");
	}

	protected function writeFileHeaderToFile($file, $header) {
		if (!$this->writeFileHeaders) {
			return;
		}

		fwrite($file, $this->getFileHeader($header));
	}

	protected function getFileHeader($header, $blankLinesAfter = 2) {
		if (! $this->writeFileHeaders) {
			return '';
		}

		$package = 'Test\Environments';
		$subpackage = capitalPDangIt($this->generationConfig['name']);
		$author = $this->generationConfig['author'];
		$copyright = $this->generationConfig['copyright'];
		$header = fullStopIt($header);

		return implode(
			"\n",
			[
					'/**',
					" * {$header}",
					' *',
					" * @package {$package}",
					" * @subpackage {$subpackage}",
					" * @author {$author}",
					" * @copyright {$copyright}",
					' *',
					' * @generated by function-mocker environment generation tool on ' . date('Y-m-d H:i:s (e)'),
					' * @link https://github.com/lucatume/function-mocker',
					' */',
				]
		) . str_repeat("\n", $blankLinesAfter);
	}

	protected function writeNamespaceToFile($fileHandle, $namespace) {
		if ($namespace === '\\' || empty($namespace)) {
			return;
		}

		fwrite($fileHandle, "namespace {$namespace};\n\n");
	}

	protected function throwNotImplementedException() {
		return [
			new Stmt\Throw_(
				new Expr\New_(
					new Name(\RuntimeException::class),
					[
						new Arg(
							new String_('Not implemented.')
						),
					]
				)
			),
		];
	}

	protected function writeClassFiles() {
		if (empty($this->classIndex)) {
			return;
		}

		$this->defaultClassSetting = [
			'removeDocBlocks' => $this->removeDocBlocks,
			'body' => $this->bodyBehaviour,
			'wrapInIf' => $this->wrapInIf,
			'autoload' => $this->autoload,
		];
		$this->normalizedClassesEntries = $this->normalizeEntries($this->classesToFind, $this->defaultClassSetting);

		foreach ($this->classIndex as $name => $classEntry) {
			$this->writeClassFile($classEntry, $name);
		}
	}

	protected function writeClassFile($classEntry, $name) {
		/*
		 * @var string $file
		 * @var Stmt $stmt
		 * @var Namespace_ $namespace
		 */
		list($file, $stmt, $namespace) = array_values($classEntry);
		$classFile = $this->destinationSrc . '/' . str_replace('\\', '/', $name) . '.php';
		$thisConfig = isset($this->normalizedClassesEntries[ $name ]) ?
			$this->normalizedClassesEntries[ $name ]
			: (object)$this->defaultClassSetting;
		$generatedConfig = $thisConfig;

		if (empty($thisConfig->autoload)) {
			$this->filesToInclude[] = $classFile;
		} else {
			$this->autoloadClasses[] = $name;
		}

		$generatedConfig->removeDocBlocks = isset($thisConfig->removeDocBlocks) ? (bool)$thisConfig->removeDocBlocks : false;
		if ((bool)$thisConfig->removeDocBlocks) {
			$stmt->setAttribute('comments', []);
			array_walk(
				$stmt->stmts,
				function (Stmt &$stmt) {
					$stmt->setAttribute('comments', []);
				}
			);
		}

		removeFinalFromClass($stmt);
		removeFinalFromClassMethods($stmt);
		openPrivateClassMethods($stmt);

		if ($thisConfig->body === 'throw') {
			$generatedConfig->body = 'throw';
			array_walk(
				$stmt->stmts,
				function (Stmt &$stmt) {
					if ($stmt instanceof Stmt\ClassMethod) {
						$stmt->stmts = $this->throwNotImplementedException();
					}
				}
			);
		} elseif ($thisConfig->body === 'empty') {
			$generatedConfig->body = 'empty';
			array_walk(
				$stmt->stmts,
				function (Stmt &$stmt) {
					if ($stmt instanceof Stmt\ClassMethod) {
						$stmt->stmts = [];
					}
				}
			);
		} else {
			$generatedConfig->body = 'copy';
		}

		$generatedConfig->wrapInIf = isset($thisConfig->wrapInIf) ? (bool)$generatedConfig->wrapInIf : true;
		if ((bool)$thisConfig->wrapInIf) {
			$namespaceString = $namespace instanceof Namespace_ ? $namespace->name : null;
			$stmt = wrapClassInIfBlock($stmt, $name, $namespaceString);
		}

		$classCode = $this->codePrinter->prettyPrint([$stmt]);

		if (!is_dir(\dirname($classFile))) {
			if (!mkdir(\dirname($classFile), 0777, true) && !is_dir(\dirname($classFile))) {
				throw new \RuntimeException(sprintf('Directory "%s" was not created', \dirname($classFile)));
			}
		}

		$generatedConfig->source = findRelativePath($this->destinationSrc, $file);

		$fileHandle = fopen($classFile, 'wb');
		$this->writePhpOpeningTagToFile($fileHandle);
		$this->writeFileHeaderToFile($fileHandle, "{$name} class.");
		fwrite($fileHandle, $classCode);
		fclose($fileHandle);
		$this->generationConfig->addClassConfig(
			$name, orderAndFilterArray(
				[
					'removeDocBlocks',
					'body',
					'wrapInIf',
					'autoload',
					'source',
				], (array)$generatedConfig
			)
		);
	}

	protected function writeEnvBootstrapFile() {
		$openingPhpTag = "<?php\n";
		$extraLines = [];
		$name = capitalPDangIt($this->generationConfig['name']);

		if (!empty($this->autoloadClasses)) {
			$extraLines = $this->writeEnvAutoloaderFile($name, $openingPhpTag);
		}

		$requireLines = $this->compileIncludePaths($this->bootstrapFile, array_unique($this->filesToInclude));
		$bootstrapCode = $openingPhpTag;

		if ($this->writeFileHeaders) {
			$headerLines = explode("\n", $this->getFileHeader($name . ' environment bootstrap file.'));
			$requireLines = array_merge($headerLines, $requireLines);
		}

		$bootstrapCode .= implode("\n", $requireLines);
		$bootstrapCode .= "\n\n" . ( is_array($extraLines) ? implode("\n", $extraLines) : $extraLines );

		file_put_contents($this->bootstrapFile, $bootstrapCode, LOCK_EX);

		return $extraLines;
	}

	protected function writeEnvAutoloaderFile($name, $openingPhpTag) {
		$autoloaderName = camelCase($this->generationConfig['name']) . 'EnvAutoloader';
		$autoloaderFile = \dirname($this->bootstrapFile) . '/' . $autoloaderName . '.php';
		$this->filesToInclude[] = $autoloaderFile;
		$template = new EnvAutoloader();
		$classMap = array_combine(
			$this->autoloadClasses,
			array_map(
				function (string $class) {
					return trim(str_replace('\\', '/', $class), '/');
				},
				$this->autoloadClasses
			)
		);
		ksort($classMap);
		$autoloadCode = $template->set('header', $this->getFileHeader($name . ' environment autoloader.', 1))->set('id', $autoloaderName)->set('name', capitalPDangIt($this->envName))->set('classMap', $classMap)->render();
		file_put_contents($autoloaderFile, $openingPhpTag . $autoloadCode, LOCK_EX);

		return $template->renderExtraLines();
	}

	/**
	 * @param $bootstrap
	 * @param $filesToInclude
	 *
	 * @return array
	 */
	protected function compileIncludePaths($bootstrap, $filesToInclude) {
		$requireLines = array_map(
			function ($file) use ($bootstrap) {
				$relativePath = findRelativePath(dirname($bootstrap), $file);

				return "require_once __DIR__ . '/{$relativePath}';";
			},
			$filesToInclude
		);

		return $requireLines;
	}

	protected function writeGenerationConfigJsonFile() {
		unset($this->generationConfig['save']);
		if ($this->writeFileHeaders) {
			$this->generationConfig['timestamp'] = time();
			$this->generationConfig['date'] = date('Y-m-d H:i:s (e)', $this->generationConfig['timestamp']);
		}

		$this->generationConfig['source'] = array_values(
			array_unique(
				array_map(
					function ($source) {
						return findRelativePath($this->destination, $source);
					},
					$this->source
				)
			)
		);
		$this->generationConfig['bootstrap'] = findRelativePath($this->destination, $this->bootstrapFile);

		if (!$this->writeDestination) {
			unset($this->generationConfig['destination']);
		} else {
			$this->generationConfig['destination'] = findRelativePath(
				getcwd(),
				$this->generationConfig['destination']
			);
		}

		$orderedGenerationConfig = orderAndFilterArray(
			[
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
			], $this->generationConfig->toArray()
		);
		$saveConfigPath = $this->destination . '/generation-config.json';
		file_put_contents(
			$saveConfigPath,
			json_encode($orderedGenerationConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
		);
	}
}
