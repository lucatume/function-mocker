<?php
	/**
	 * Created by PhpStorm.
	 * User: Luca
	 * Date: 24/12/14
	 * Time: 18:41
	 */

	namespace tests\tad\FunctionMocker;


	use tad\FunctionMocker\Utils;

	class UtilsTest extends \PHPUnit\Framework\TestCase {

		private $rootDir;

		public function setUp(): void {
			$this->rootDir = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
		}

		public function pathArrays() {
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
		public function it_should_return_properly_filtered_paths_arrays( $in, $out ) {
			$in  = str_replace( '/', DIRECTORY_SEPARATOR, $in );
			$out = str_replace( '/', DIRECTORY_SEPARATOR, $out );

			$this->assertEquals( $out, Utils::filterPathListFrom( $in, $this->rootDir ) );
		}
	}
