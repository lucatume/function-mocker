<?php
	/**
	 * Created by PhpStorm.
	 * User: Luca
	 * Date: 24/12/14
	 * Time: 18:41
	 */

	namespace tests\tad\FunctionMocker; \Patchwork\Interceptor\deployQueue();


	use tad\FunctionMocker\Utils;

	class UtilsTest extends \PHPUnit_Framework_TestCase {

		private $rootDir;

		public function setUp() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$this->rootDir = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
		}

		public function pathArrays() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$rootDir = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );

			return [
				[ [ 'vendor/some' ], [ ] ],
				[ [ 'vendor/bin' ], [ $rootDir . '/vendor/bin' ] ],
				[ [ 'vendor/bin', 'vendor/some' ], [ $rootDir . '/vendor/bin' ] ]
			];
		}

		/**
		 * @test
		 * it should return properly filtered paths arrays
		 * @dataProvider pathArrays
		 */
		public function it_should_return_properly_filtered_paths_arrays( $in, $out ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$this->assertEquals( $out, Utils::filterPathListFrom( $in, $this->rootDir ) );
		}
	}\Patchwork\Interceptor\deployQueue();
