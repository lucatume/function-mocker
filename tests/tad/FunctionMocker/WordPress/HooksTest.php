<?php

namespace tad\FunctionMocker\WordPress;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use tad\FunctionMocker\FunctionMocker;

class HooksTest extends TestCase
{
	protected function setUp()
	{
		FunctionMocker::setUp();
	}

	/**
	 * It should allow stubbing the action related functions
	 *
	 * @test
	 */
	public function should_allow_stubbing_the_action_related_functions() {
		FunctionMocker::add_action( 'some_action', Argument::cetera() )->willReturn( 'added' );
		FunctionMocker::do_action( 'some_action' )->willReturn( 'foo' );
		FunctionMocker::did_action( 'some_action' )->willReturn( 'did it!' );

		$this->assertEquals( 'added', add_action( 'some_action', '__return_false' ) );
		$this->assertEquals( 'foo', do_action( 'some_action' ) );
		$this->assertEquals( 'did it!', did_action( 'some_action' ) );
	}

	/**
	 * It should allow stubbing the filter related functions
	 *
	 * @test
	 */
	public function should_allow_stubbing_the_filter_related_functions() {
		FunctionMocker::add_filter( 'some_filter', Argument::cetera() )->willReturn( 'added' );
		FunctionMocker::apply_filters( 'some_filter', Argument::type('string') )->willReturn( 'foo' );

		$this->assertEquals( 'added', add_filter( 'some_filter', '__return_false' ) );
		$this->assertEquals( 'foo', apply_filters( 'some_filter', 'some_string' ) );
	}

    /**
     * It should allow adding actions
     *
     * @test
     */
    public function should_allow_adding_actions()
    {
    	add_action('some_action', function () use (&$buffer) {
    		$this->assertTrue(doing_action('some_action'));
    		$this->assertFalse(doing_action('another_action'));
    		$buffer = func_get_args();
    	});

    	do_action('some_action', 'one');

    	$this->assertEquals(['one'], $buffer);
    	$this->assertTrue((bool) did_action('some_action'));
    }

    /**
     * It should allow adding filters
     *
     * @test
     */
    public function should_allow_adding_filters()
    {
    	add_filter('some_filter', function ($input) {
    		$this->assertTrue(doing_filter('some_filter'));
    		$this->assertFalse(doing_filter('another_filter'));
    		return $input . 'bar';
    	});

    	$this->assertEquals('foobar', apply_filters('some_filter', 'foo'));
    }

    protected function tearDown()
    {
    	FunctionMocker::tearDown();
    }
}
