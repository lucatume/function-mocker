<?php

namespace Examples\WoocommerceEnv;


class PackagingException extends \Exception {

	public static function because_the_product_does_not_fit_in_any_box() {
		return new static;
	}
}