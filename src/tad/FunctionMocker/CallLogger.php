<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 06/11/14
 * Time: 20:55
 */

namespace tad\FunctionMocker;


interface CallLogger {

	public function called( array $args = null );
}