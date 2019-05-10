<?php

namespace tad\FunctionMocker\Templates;

use PHPUnit\Framework\TestCase;

require_once _data_dir('DummyTemplate.php');

class TemplateTest extends TestCase
{

    /**
     * Should render the class template
     *
     * @test
     */
    public function should_render_the_class_template()
    {
        $template = $this->make('The {{adjective}} fox jumps over the lazy dog.');

        $this->assertEquals('The  fox jumps over the lazy dog.', $template->render());

        $template->set('adjective', 'quick brown');

        $this->assertEquals('The quick brown fox jumps over the lazy dog.', $template->render());
    }

    protected function make($template, array $extraLines = [])
    {
        return new DummyTemplate($template, $extraLines);
    }

    /**
     * Should render a template extra lines.
     *
     * @test
     */
    public function should_render_a_template_extra_lines_()
    {
        $template = $this->make('', [ 'My name is {{myName}}', 'Your name is {{yourName}}' ]);

        $this->assertEquals("My name is \nYour name is ", $template->renderExtraLines());

        $template->set('myName', 'John')
                 ->set('yourName', 'Jane');

        $this->assertEquals("My name is John\nYour name is Jane", $template->renderExtraLines());
    }
}
