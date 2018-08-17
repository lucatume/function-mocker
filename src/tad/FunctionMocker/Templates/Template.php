<?php

namespace tad\FunctionMocker\Templates;

use Handlebars\Handlebars;

abstract class Template {

	protected static $template = '';
	protected $data = [];
	/**
	 * @var \Handlebars\Handlebars
	 */
	protected $hb;

	protected $extraLines = [];

	public function render() {
		$this->hb = $this->hb ?: new Handlebars();

		return $this->hb->render( static::$template, $this->data );
	}

	public function getExtraLines() {
		$this->hb = $this->hb ?: new Handlebars();

		return implode( "\n", array_map( function ( $line ) {
			return $this->hb->render( $line, $this->data );
		}, $this->extraLines ) );
	}

	public function set( $key, $value ) {
		$this->data[ $key ] = $value;

		return $this;
	}
}
