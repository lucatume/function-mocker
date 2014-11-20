<?php
	/**
	 * Created by PhpStorm.
	 * User: Luca
	 * Date: 20/11/14
	 * Time: 17:21
	 */

	namespace src\tad\FunctionMocker\Template\Wrapping;


	interface Extender {

		public function getExtendingClass();
		public function getExtendingMethods();
	}