<?php

namespace Examples\WoocommerceEnv;

class Boxer {

	/**
	 * @var Box[]
	 */
	protected $boxes;

	public function get_box_for_product( $product_id, $length_unit = 'in', $weight_unit = 'lbs' ) {
		$product = wc_get_product( $product_id );

		if ( ! $product instanceof \WC_Product ) {
			throw new \RuntimeException( "Product with ID {$product_id} does not exist." );
		}

		$product_dimensions = $this->get_original_product_dimensions( $product );

		$imperial_product_dimensions = $this->normalize_dimensions( $length_unit, $weight_unit, $product_dimensions );

		$box = $this->find_box_fitting_dimensions( $imperial_product_dimensions );

		return $box;
	}

	protected function get_original_product_dimensions( \WC_Product $product ) {
		$product_dimensions = [
			$product->get_width(),
			$product->get_height(),
			$product->get_length(),
			$product->get_weight(),
		];

		return $product_dimensions;
	}

	protected function normalize_dimensions( $length_unit, $weight_unit, $dimensions ) {
		$imperial_product_dimensions = [
			wc_get_dimension( $dimensions[0], $length_unit, Box::INCH ),
			wc_get_dimension( $dimensions[1], $length_unit, Box::INCH ),
			wc_get_dimension( $dimensions[2], $length_unit, Box::INCH ),
			wc_get_weight( $dimensions[3], $weight_unit, Box::LB ),
		];

		return $imperial_product_dimensions;
	}

	protected function find_box_fitting_dimensions( $dimensions ) {
		foreach ( $this->boxes as $this_box ) {
			$box_dimensions = $this_box->dimensions();

			for ( $i = 0, $iMax = \count( $dimensions ); $i < $iMax; $i ++ ) {
				if ( $dimensions[ $i ] > $box_dimensions[ $i ] ) {
					continue 2;
				}
			}

			return $this_box;
		}

		return new NoFitBox();
	}

	public function set_boxes( $boxes ) {
		foreach ( $boxes as $box ) {
			if ( ! $box instanceof Box ) {
				throw new \InvalidArgumentException( 'All boxes should be Boxes' );
			}
		}

		uasort( $boxes, function ( Box $box_a, Box $box_b ) {
			$a = array_sum( $box_a->dimensions() );
			$b = array_sum( $box_b->dimensions() );

			if ( $a === $b ) {
				return 0;
			}

			return ( $a < $b ) ? - 1 : 1;
		} );

		$this->boxes = $boxes;
	}
}