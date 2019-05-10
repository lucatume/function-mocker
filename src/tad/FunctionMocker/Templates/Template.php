<?php
/**
 * Base code template.
 *
 * @package    FunctionMocker
 * @subpackage CLI
 * @author     Luca Tumedei <luca@theaveragedev.com>
 * @copyright  2018 Luca Tumedei
 */

namespace tad\FunctionMocker\Templates;

use Handlebars\Handlebars;

/**
 * Class Template
 */
abstract class Template
{

    /**
     * The output template for this class.
     *
     * @var string
     */
    protected static $template = '';

    /**
     * An array of extra lines, each one an Handlebar template in itself, for the template.
     *
     * @var array
     */
    protected static $extraLines = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var \Handlebars\Handlebars
     */
    protected $hb;

    /**
     * Renders, using Handlebar, the template and returns it.
     *
     * @return string
     */
    public function render()
    {
        $this->hb = $this->hb ?: new Handlebars();

        return $this->hb->render(static::$template, $this->data);
    }

    /**
     * Renders, using Handlebar, each extra line for the template, glues them with blank lines and returns them.
     *
     * @return string
     */
    public function renderExtraLines()
    {
        $this->hb = $this->hb ?: new Handlebars();

        return implode(
            "\n",
            array_map(
                function ($line) {
                    return $this->hb->render($line, $this->data);
                },
                static::$extraLines
            )
        );
    }

    /**
     * Sets a single data key on the template.
     *
     * @param string $key   The data key.
     * @param mixed  $value The data value.
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }
}
