<?php

namespace tad\FunctionMocker\Templates;

class DummyTemplate extends Template {

	public function __construct( $template, array $extraLines = null ) {
		static::$template = $template;
		static::$extraLines = $extraLines;
	}
}