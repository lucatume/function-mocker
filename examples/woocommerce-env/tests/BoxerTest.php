<?php

use Examples\WoocommerceEnv\Box;
use Examples\WoocommerceEnv\Boxer;
use tad\FunctionMocker\FunctionMocker;

class BoxerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 * it should be instantiatable
	 */
	public function it_should_be_instantiatable() {
		$this->assertInstanceOf( Boxer::class, new Boxer() );
	}

	public function product_dimensions() {
		return [
			'fits-in-small'            => [ 'small', [ 2, 2, 2, 1 ] ],
			'barely-fits-in-small'     => [ 'small', [ 5, 5, 5, 1 ] ],
			'small-with-medium-weight' => [ 'medium', [ 5, 5, 5, 2 ] ],
			'fits-in-medium'           => [ 'medium', [ 10, 8, 5, 2 ] ],
			'barely-fits-in-medium'    => [ 'medium', [ 12.5, 12.5, 6, 3 ] ],
			'medium-with-large-weight' => [ 'large', [ 10, 8, 5, 5 ] ],
			'fits-in-large'            => [ 'large', [ 15, 12, 8, 4 ] ],
			'barely-fits-in-large'     => [ 'large', [ 10, 15, 10, 6 ] ],
			'width-does-not-fit'       => [ 'no-fit', [ 22, 15, 10, 6 ] ],
			'height-does-not-fit'      => [ 'no-fit', [ 8, 18, 10, 6 ] ],
			'length-does-not-fit'      => [ 'no-fit', [ 8, 14, 12, 6 ] ],
			'weight-does-not-fit'      => [ 'no-fit', [ 8, 14, 9, 8 ] ],
		];
	}

	/**
	 * It should return the correct box for a product
	 *
	 * @test
	 *
	 * @dataProvider product_dimensions
	 */
	public function should_return_the_correct_box_for_a_product( $expected_type, $dimensions ) {
		$boxer = new Boxer();
		$boxer->set_boxes( [
			'small'  => new Box( 'small', [ 5, 5, 5, 1 ], Box::INCH, Box::LB ),
			'medium' => new Box( 'medium', [ 12.5, 12.5, 6, 3 ], Box::INCH, Box::LB ),
			'large'  => new Box( 'large', [ 20, 15, 10, 6 ], Box::INCH, Box::LB ),
		] );

		$product = $this->prophesize( \WC_Product::class );
		$product->get_width()->willReturn( $dimensions[0] );
		$product->get_height()->willReturn( $dimensions[1] );
		$product->get_length()->willReturn( $dimensions[2] );
		$product->get_weight()->willReturn( $dimensions[3] );

		$product_id = 23;

		FunctionMocker::wc_get_product( $product_id )->willReturn( $product->reveal() );

		$box = $boxer->get_box_for_product( $product_id, Box::INCH, Box::LB );

		$this->assertEquals( $expected_type, $box->type() );
	}

	protected function setUp() {
		FunctionMocker::setUp();
	}

	protected function tearDown() {
		FunctionMocker::tearDown();
	}
}
