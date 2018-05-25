<?php

namespace Examples\WPCore;

use tad\FunctionMocker\FunctionMocker;

class pluginTest extends \WP_UnitTestCase {

	protected $backupGlobals = false;

	function setUp() {
		// Start wrapping...
		FunctionMocker::setUp();
		// ...and then include WordPress
		parent::setUp();
	}

	/**
	 * Test it will not log if not is_admin
	 */
	public function test_it_will_not_log_if_not_admin() {
		// stub the `is_admin` function
		FunctionMocker::is_admin()->willReturn( false );
		// stub the internal `time` function
		FunctionMocker::time()->willReturn( strtotime( '2018-01-01 09:00:00' ) );
		// and then start spying the `get_transient` function...
		FunctionMocker::spy( 'get_transient' );

		logger_start();
		Logger::write( 'Something' );

		//...to make sure it's not been called
		FunctionMocker::get_transient( 'log_2018_01_01_09' )->shouldNotHaveBeenCalled();
	}

	/**
	 * Test it will log if is_admin
	 */
	public function test_it_will_log_if_is_admin() {
		// stub the `is_admin` function
		FunctionMocker::is_admin()->willReturn( true );
		// stub the internal `time` function
		FunctionMocker::time()->willReturn( strtotime( '2018-01-01 09:13:27' ) );
		// stub the `get_transient` function
		FunctionMocker::get_transient( 'log_2018_01_01_09' )->willReturn( [] );
		// and then start spying the `set_transient` function...
		FunctionMocker::spy( 'set_transient' );

		logger_start();
		Logger::write( 'Something' );

		//...to make sure it's been called the way we expect it to
		FunctionMocker::set_transient( 'log_2018_01_01_09', [ '13:27' => 'Something' ], DAY_IN_SECONDS )->shouldHaveBeenCalled();
	}

	function tearDown() {
		// Stop wrapping...
		FunctionMocker::tearDown( $this );
		// ...to let WordPress tearDown smoothly
		parent::tearDown();
	}
}
