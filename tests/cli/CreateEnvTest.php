<?php

namespace tad\FunctionMocker\CLI;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use tad\FunctionMocker\Tests\SnapshotAssertions;

class CreateEnvTest extends \PHPUnit_Framework_TestCase {

	use SnapshotAssertions;

	/**
	 * It should create a global namespace functions file
	 *
	 * @test
	 */
	public function should_create_a_global_namespace_functions_file() {
		$noConfigCommand = new CreateEnv();
		$noConfigCommand->_writeFileHeaders( false );
		$input = new ArrayInput( [
			'name'          => 'test-env',
			'source'        => _data_dir( 'env/src/global-functions.php' ),
			'--destination' => _output_dir( 'test-1' ),
		] );
		$output = new NullOutput();

		$noConfigCommand->run( $input, $output );

		$this->assertFilesSnapshot( _output_dir( 'test-1' ) );

		$withConfigCommand = new CreateEnv();
		$withConfigCommand->_writeFileHeaders( false );
		$input = new ArrayInput( [
			'name'          => 'test-env',
			'source'        => _data_dir( 'env/src/global-functions.php' ),
			'--destination' => _output_dir( 'test-2' ),
			'--config'      => _output_dir( 'test-1/generation-config.json' ),
		] );

		$withConfigCommand->run( $input, $output );

		$this->assertFilesSnapshot( _output_dir( 'test-2' ), 'test-1' );
	}

	/**
	 * /**
	 * It should create a global class file
	 *
	 * @test
	 */
	public function should_create_a_global_class_file() {
		$command = new CreateEnv();
		$command->_writeFileHeaders( false );
		$input = new ArrayInput( [
			'name'          => 'test-env',
			'source'        => _data_dir( 'env/src/GlobalNamespaceClass.php' ),
			'--destination' => _output_dir( 'test-3' ),
		] );
		$output = new NullOutput();

		$command->run( $input, $output );

		$withConfigCommand = new CreateEnv();
		$withConfigCommand->_writeFileHeaders( false );
		$input = new ArrayInput( [
			'name'          => 'test-env',
			'source'        => _data_dir( 'env/src/GlobalNamespaceClass.php' ),
			'--destination' => _output_dir( 'test-4' ),
			'--config'      => _output_dir( 'test-3/generation-config.json' ),
		] );

		$withConfigCommand->run( $input, $output );

		$this->assertFilesSnapshot( _output_dir( 'test-4' ), 'test-3' );
	}

	/**
	 * It should open up final classes
	 *
	 * @test
	 */
	public function should_open_up_final_classes() {
		$command = new CreateEnv();
		$command->_writeFileHeaders( false );
		$input = new ArrayInput( [
			'name'          => 'test-env',
			'source'        => _data_dir( 'env/src/FinalClass.php' ),
			'--destination' => _output_dir( 'test-5' ),
		] );
		$output = new NullOutput();

		$command->run( $input, $output );

		$this->assertFilesSnapshot( _output_dir( 'test-5' ) );
	}

	/**
	 * It should open up final methods
	 *
	 * @test
	 */
	public function should_open_up_final_methods() {
		$command = new CreateEnv();
		$command->_writeFileHeaders( false );
		$input = new ArrayInput( [
			'name'          => 'test-env',
			'source'        => _data_dir( 'env/src/ClassWFinalMethods.php' ),
			'--destination' => _output_dir( 'test-6' ),
		] );
		$output = new NullOutput();

		$command->run( $input, $output );

		$this->assertFilesSnapshot( _output_dir( 'test-6' ) );
	}

	/**
	 * It should open up private methods to protected
	 *
	 * @test
	 */
	public function should_open_up_private_methods_to_protected() {
		$command = new CreateEnv();
		$command->_writeFileHeaders( false );
		$input = new ArrayInput( [
			'name'          => 'test-env',
			'source'        => _data_dir( 'env/src/ClassWPrivateMethods.php' ),
			'--destination' => _output_dir( 'test-7' ),
		] );
		$output = new NullOutput();

		$command->run( $input, $output );

		$this->assertFilesSnapshot( _output_dir( 'test-7' ) );
	}

	/**
	 * It should correctly generate env for namespaced functions
	 *
	 * @test
	 */
	public function should_correctly_generate_env_for_namespaced_functions() {
		$command = new CreateEnv();
		$command->_writeFileHeaders( false );
		$input = new ArrayInput( [
			'name'          => 'test-env',
			'source'        => _data_dir( 'env/src/namespaced-functions.php' ),
			'--destination' => _output_dir( 'test-8' ),
		] );
		$output = new NullOutput();

		$command->run( $input, $output );

		$this->assertFilesSnapshot( _output_dir( 'test-8' ) );
	}

	/**
	 * It should correcly parse and apply configuration parameters
	 *
	 * @test
	 */
	public function should_correcly_parse_and_apply_configuration_parameters() {
		$command = new CreateEnv();
		$command->_writeFileHeaders( false );
		$input = new ArrayInput( [
			'name'          => 'test-env',
			'source'        => [
				_data_dir( 'env/src/global-functions.php' ),
				_data_dir( 'env/src/namespaced-functions.php' ),
				_data_dir( 'env/src/ClassWPrivateMethods.php' ),
				_data_dir( 'env/src/ClassWFinalMethods.php' ),
			],
			'--destination' => _output_dir( 'test-9' ),
			'--config'      => _data_dir( 'env/generation-config-1.json' ),
		] );
		$output = new NullOutput();

		$command->run( $input, $output );

		$this->assertFilesSnapshot( _output_dir( 'test-9' ) );
	}

	/**
	 * It should correctly handle interfaces and traits
	 *
	 * @test
	 */
	public function should_correctly_handle_interfaces_and_traits() {
		$command = new CreateEnv();
		$command->_writeFileHeaders( false );
		$input = new ArrayInput( [
			'name'          => 'test-env',
			'source'        => [
				_data_dir( 'env/src/GlobalAbstractClass.php' ),
				_data_dir( 'env/src/NamespacedAbstractClass.php' ),
				_data_dir( 'env/src/GlobalInterface.php' ),
				_data_dir( 'env/src/NamespacedInterface.php' ),
				_data_dir( 'env/src/GlobalTrait.php' ),
				_data_dir( 'env/src/NamespacedTrait.php' ),
			],
			'--destination' => _output_dir( 'test-10' ),
		] );
		$output = new NullOutput();

		$command->run( $input, $output );

		$this->assertFilesSnapshot( _output_dir( 'test-10' ) );
	}

	/**
	 * It should allow resolving dependencies
	 *
	 * @test
	 */
	public function should_allow_resolving_dependencies() {
		$command = new CreateEnv();
		$command->_writeFileHeaders( false );
		$input = new ArrayInput( [
			'name'          => 'test-env',
			'source'        => [
				_data_dir( 'env/src/DependingClass.php' ),
				_data_dir( 'env/src/depending-functions.php' ),
			],
			'--destination' => _output_dir( 'test-11' ),
			'--with-dependencies' => true,
		] );
		$output = new NullOutput();

		$command->run( $input, $output );

		$this->assertFilesSnapshot( _output_dir( 'test-11' ) );
	}
}
