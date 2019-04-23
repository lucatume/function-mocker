<?php

namespace tad\FunctionMocker\CLI;

use Symfony\Component\Console\Input\InputInterface;
use tad\FunctionMocker\CLI\Exceptions\RuntimeException;
use function tad\FunctionMocker\expandTildeIn;
use function tad\FunctionMocker\validateFileOrDir;
use function tad\FunctionMocker\validateJsonFile;

class RunConfiguration implements \ArrayAccess {

	protected $config = [];

	public static function fromInput( InputInterface $input ) {
		$instance = new static();
		$instance->config = $instance->initFromInput($input);
		$instance->config['functions'] = isset($instance->config['functions']) ?
			$instance->config['functions']
			: [];
		$instance->config['classes'] = isset($instance->config['classes']) ?
			$instance->config['classes']
			: [];

		return $instance;
	}

	protected function initFromInput( InputInterface $input ) {
		$name = $input->getArgument('name');
		$inputDestination = $input->hasOption('destination') ? $input->getOption('destination') : '/tests/envs';

		$cliConfig = [
			'name'              => $input->getArgument('name'),
			'source'            => $input->getArgument('source'),
			'destination'       => $inputDestination,
			'save'              => $input->hasOption('save') ? $input->getOption('save') : null,
			'with-dependencies' => $input->getOption('with-dependencies'),
			'author'            => $input->getOption('author'),
			'copyright'         => $input->getOption('copyright'),
		];

		foreach ([ 'source', 'destination' ] as $key) {
			if (! isset($cliConfig[ $key ])) {
				continue;
			}

			$cliConfig[ $key ] = expandTildeIn($cliConfig[ $key ]);
		}

		$configFile = $input->getOption('config');

		$configFileConfig = [
			'removeDocBlocks' => false,
			'wrapInIf'        => true,
			'body'            => 'copy',
			'autoload'        => true,
		];

		if ($configFile) {
			$configFile = validateFileOrDir($configFile, "JSON configuration file");
			$configFileConfig = validateJsonFile($configFile);
			$configFileConfig['configFileDir'] = \tad\FunctionMocker\realpath(\dirname($configFile));
		}

		$configFileConfig['_readme'] = [
			"This file defines the {$name} testing environment generation rules.",
			'Read more about it at https://github.com/lucatume/function-mocker.',
			'This file was automatically @generated.',
		];

		if (empty($cliConfig['source'])) {
			unset($cliConfig['source']);
		}

		$configFileSources = ! empty($configFileConfig['source']) ? (array)$configFileConfig['source'] : [];
		unset($configFileConfig['source']);

		$config = array_merge(
			$configFileConfig,
			array_filter(
				$cliConfig,
				function ( $v ) {
					return null !== $v;
				}
			)
		);

		$config['source'] = ! empty($config['source']) ? array_merge(
			(array)$config['source'],
			$configFileSources
		) : $configFileSources;

		if (empty($config['source'])) {
			throw RuntimeException::becauseNoSourcesWereSpecified();
		}

		$config['source'] = array_unique($config['source']);

		return $config;
	}

	/**
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 *                      </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since  5.0.0
	 */
	public function offsetExists( $offset ) {
		return isset($this->config[ $offset ]);
	}

	/**
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 *                      </p>
	 *
	 * @return mixed Can return all value types.
	 * @since  5.0.0
	 */
	public function offsetGet( $offset ) {
		return $this->config[ $offset ];
	}

	/**
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to assign the value to.
	 *                      </p>
	 * @param mixed $value  <p>
	 *                      The value to set.
	 *                      </p>
	 *
	 * @return void
	 * @since  5.0.0
	 */
	public function offsetSet( $offset, $value ) {
		$this->config[ $offset ] = $value;
	}

	/**
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 *                      </p>
	 *
	 * @return void
	 * @since  5.0.0
	 */
	public function offsetUnset( $offset ) {
		unset($this->config[ $offset ]);
	}

	public function addFunctionConfig( string $name, $functionConfig ) {
		$this->config['functions'][ $name ] = $functionConfig;
	}

	public function addClassConfig( string $name, $classConfig ) {
		$this->config['classes'][ $name ] = $classConfig;
	}

	public function toArray() {
		if (!empty($this->config['classes'])) {
			ksort($this->config['classes']);
		}

		if (!empty($this->config['functions'])) {
			ksort($this->config['functions']);
		}

		return $this->config;
	}
}
