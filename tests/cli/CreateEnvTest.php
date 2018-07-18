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
}
