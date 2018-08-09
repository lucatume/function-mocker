<?php

namespace tad\FunctionMocker\Templates;

use Handlebars\Handlebars;

abstract class Template
{

	protected static $template = '';

	public function render($data) {
		$hb = new Handlebars();

		return $hb->render(static::$template, $data);
	}
}
