<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 05/11/14
 * Time: 08:48
 */

namespace tad\FunctionMocker;


interface CallVerifier {

	/**
	 * Checks if the function or method was called the specified number
	 * of times.
	 *
	 * @param  int $times
	 *
	 * @return void
	 */
	public function wasCalledTimes( $times );

	/**
	 * Checks if the function or method was called with the specified
	 * arguments a number of times.
	 *
	 * @param  array $args
	 * @param  int   $times
	 *
	 * @return void
	 */
	public function wasCalledWithTimes( array $args = array(), $times );

	/**
	 * Checks that the function or method was not called.
	 *
	 * @return void
	 */
	public function wasNotCalled();

	/**
	 * Checks that the function or method was not called with
	 * the specified arguments.
	 *
	 * @param  array $args
	 *
	 * @return void
	 */
	public function wasNotCalledWith( array $args = null );

	/**
	 * Checks if a given function or method was called just one time.
	 */
	public function wasCalledOnce();
}