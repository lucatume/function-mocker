<?php

namespace tad\FunctionMocker\WPBrowser;

use Codeception\Event\SuiteEvent;
use Codeception\Exception\ExtensionException;
use Codeception\Suite;
use PHPUnit\Framework\TestCase;

class ExtensionTest extends TestCase {

	/**
	 * It should throw if the initFile parameter is not specified
	 *
	 * @test
	 */
	public function should_throw_if_the_init_file_parameter_is_not_specified() {
		$event = $this->prophesize(SuiteEvent::class);

		$extension = new Extension([], []);

		$this->expectException(ExtensionException::class);

		$extension->onModuleInit($event->reveal());
	}

	/**
	 * It should throw if the initFile does not exist
	 *
	 * @test
	 */
	public function should_throw_if_the_init_file_does_not_exist() {
		$event = $this->prophesize(SuiteEvent::class);

		$extension = new Extension(['initFile' => __DIR__ . 'foo.php'], []);

		$this->expectException(ExtensionException::class);

		$extension->onModuleInit($event->reveal());
	}

	/**
	 * It should throw if the initFile is not a file
	 *
	 * @test
	 */
	public function should_throw_if_the_init_file_is_not_a_file() {
		$event = $this->prophesize(SuiteEvent::class);

		$extension = new Extension(['initFile' => __DIR__], []);

		$this->expectException(ExtensionException::class);

		$extension->onModuleInit($event->reveal());
	}

	/**
	 * It should resolve the initFile from current working directory
	 *
	 * @test
	 */
	public function should_resolve_the_init_file_from_current_working_directory() {
		$event = $this->prophesize(SuiteEvent::class);

		$extension = new Extension(['initFile' => 'tests/_data/some-file.php'], []);

		$extension->onModuleInit($event->reveal());
	}

	/**
	 * It should not include the init file if the current suite is not enabled
	 *
	 * @test
	 */
	public function should_not_include_the_init_file_if_the_current_suite_is_not_enabled() {
		/** @var Suite $suite */
		$suite = $this->prophesize(Suite::class);
		$suite->getName()->willReturn('bar');
		$event = $this->prophesize(SuiteEvent::class);
		$event->getSuite()->willReturn($suite->reveal());

		$extension = new Extension(['initFile' => 'tests/_data/some-file.php', 'suites' => ['foo']], []);

		$this->assertFalse($extension->onModuleInit($event->reveal()));
	}

	/**
	 * It should include the init file if the current suite is enabled
	 *
	 * @test
	 */
	public function should_include_the_init_file_if_the_current_suite_is_enabled() {
		/** @var Suite $suite */
		$suite = $this->prophesize(Suite::class);
		$suite->getName()->willReturn('foo');
		$event = $this->prophesize(SuiteEvent::class);
		$event->getSuite()->willReturn($suite->reveal());

		$extension = new Extension(['initFile' => 'tests/_data/some-file.php', 'suites' => ['foo']], []);

		$this->assertTrue($extension->onModuleInit($event->reveal()));
	}

	/**
	 * It should accept the suites parameter in string format
	 *
	 * @test
	 */
	public function should_accept_the_suites_parameter_in_string_format() {
		/** @var Suite $suite */
		$suite = $this->prophesize(Suite::class);
		$suite->getName()->willReturn('foo');
		$event = $this->prophesize(SuiteEvent::class);
		$event->getSuite()->willReturn($suite->reveal());

		$extension = new Extension(['initFile' => 'tests/_data/some-file.php', 'suites' => 'foo'], []);

		$this->assertTrue($extension->onModuleInit($event->reveal()));
	}
}
