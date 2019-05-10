<?php

namespace tad\FunctionMocker;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;

class codeManipulationTest extends TestCase
{
    /**
     * Should correctly resolve global namespaces
     *
     * @test
     */
    public function should_correctly_resolve_global_namespaces()
    {
        $this->assertEquals('\\foo', resolveNamespace(new Name('foo')));
        $this->assertEquals('\\foo', resolveNamespace(new Name\FullyQualified('\\foo')));
    }

    /**
     * Should correctly resolve fully-qualified name
     *
     * @test
     */
    public function should_correctly_resolve_fully_qualified_name()
    {
        $this->assertEquals('\\foo\\bar\\baz', resolveNamespace(new Name\FullyQualified('\\foo\\bar\\baz')));
    }

    /**
     * Should correctly resolve fully qualified names with fully qualified namespace
     *
     * @test
     */
    public function should_correctly_resolve_fully_qualified_names_with_fully_qualified_namespace()
    {
        $this->assertEquals(
            '\\foo\\bar\\baz',
            resolveNamespace(
                new Name\FullyQualified('\\foo\\bar\\baz'),
                new Namespace_(new Name\FullyQualified('\\foo\\bar'))
            )
        );
    }

    /**
     * Should correctly resolve fully qualified names with relative namespace
     *
     * @test
     */
    public function should_correctly_resolve_fully_qualified_names_with_relative_namespace()
    {
        $this->assertEquals(
            '\\Acme\\Company\\Package\\ClassOne',
            resolveNamespace(
                new Name\FullyQualified('\\Acme\\Company\\Package\\ClassOne'),
                new Namespace_(new Name\Relative('Company\\Package'))
            )
        );
    }

    /**
     * Should correctly resolve relative names with fully qualified namespace
     *
     * @test
     */
    public function should_correctly_resolve_relative_names_with_fully_qualified_namespace()
    {
        $this->assertEquals(
            '\\Acme\\Company\\Package\\ClassOne',
            resolveNamespace(
                new Name\Relative('Package\\ClassOne'),
                new Namespace_(new Name\FullyQualified('\\Acme\\Company\\Package'))
            )
        );
    }

    /**
     * Should correctly resolve fully qualified name with relative namespace
     *
     * @test
     */
    public function should_correctly_resolve_fully_qualified_name_with_relative_namespace()
    {
        $this->assertEquals(
            '\\Acme\\Company\\Package\\ClassOne',
            resolveNamespace(
                new Name\FullyQualified('\\Acme\\Company\\Package\\ClassOne'),
                new Namespace_(new Name\Relative('Company\\Package'))
            )
        );
    }

    /**
     * Should throw if trying to get statements from non file
     *
     * @test
     */
    public function should_throw_if_trying_to_get_statements_from_non_file()
    {
        $this->expectException(\InvalidArgumentException::class);

        getAllFileStmts('foo.php');
    }

    /**
     * Should allow parsing top level statements in a list of files
     *
     * @test
     */
    public function should_allow_parsing_top_level_statements_in_a_list_of_files()
    {
        $firstFile = _data_dir('global-functions.php');
        $secondFile = _data_dir('namespaced-functions.php');

        $this->assertCount(6, getAllFileStmts($firstFile));
        $this->assertCount(6, getAllFileStmts([$firstFile]));
        $this->assertCount(7, getAllFileStmts([$firstFile, $secondFile]));
    }

    /**
     * Should allow wrapping statements
     *
     * @test
     */
    public function should_allow_wrapping_statements()
    {
        $wrapped = wrapStmtInIfNotBlock(new Function_('someFunction'), 'someFunction', 'function_exists');

        $expected = <<< PHP
if (!function_exists('someFunction')) {
    function someFunction()
    {
    }
}
PHP;
        $printer = new Standard();
        $this->assertEquals($expected, $printer->prettyPrint([$wrapped]));
    }

    /**
     * Should correctly wrap function statements in if blocks
     *
     * @test
     */
    public function should_correctly_wrap_function_statements_in_if_blocks()
    {
        $stmt = new Function_('someFunction');
        $expected = <<< PHP
if (!\\function_exists('\\\\Acme\\\\someFunction')) {
    function someFunction()
    {
    }
}
PHP;
        $printer = new Standard();
        $block = wrapFunctionInIfBlock($stmt, '\\Acme\\someFunction', '\\Acme');
        $this->assertEquals($expected, $printer->prettyPrint([$block]));

        $stmt = new Function_('\\Acme\\someFunction');
        $expected = <<< PHP
if (!\\function_exists('\\\\Acme\\\\someFunction')) {
    function someFunction()
    {
    }
}
PHP;
        $printer = new Standard();
        $block = wrapFunctionInIfBlock($stmt, '\\Acme\\someFunction', '\\Acme');
        $this->assertEquals($expected, $printer->prettyPrint([$block]));
    }

    public function classTypesAndChecks()
    {
        return [
            'global-class' => [Class_::class, 'SomeClass', "class_exists('SomeClass')", ''],
            'fq-global-class' => [Class_::class, '\\SomeClass', "class_exists('\\\\SomeClass')", ''],
            'namespaced-class' => [Class_::class, '\\Acme\\SomeClass', "\\class_exists('\\\\Acme\\\\SomeClass')", '\\Acme'],
            'global-interface' => [Interface_::class, 'SomeInterface', "interface_exists('SomeInterface')", ''],
            'fq-global-interface' => [Interface_::class, '\\SomeInterface', "\\interface_exists('\\\\SomeInterface')", '\\Acme'],
            'namespaced-interface' => [Interface_::class, '\\Acme\\SomeInterface', '\\interface_exists(\'\\\\Acme\\\\SomeInterface\')', '\\Acme'],
            'global-trait' => [Trait_::class, 'SomeTrait', "trait_exists('SomeTrait')", ''],
            'fq-global-trait' => [Trait_::class, '\\SomeTrait', '\trait_exists(\'\\\\SomeTrait\')', '\\Acme'],
            'namespaced-trait' => [Trait_::class, '\\Acme\\SomeTrait', '\trait_exists(\'\\\\Acme\\\\SomeTrait\')', '\\Acme'],
        ];
    }

    /**
     * Should correctly wrap class statements
     *
     * @test
     *
     * @dataProvider classTypesAndChecks
     */
    public function should_correctly_wrap_class_statements($class, $fqName, $check, $namespace)
    {
        switch ($class) {
            case Class_::class:
                $type = 'class';
                break;
            case Interface_::class:
                $type = 'interface';
                break;
            case Trait_::class:
                $type = 'trait';
                break;
        }
        $stmt = new $class($fqName);
        $nameFrags = explode('\\', $fqName);
        $name = end($nameFrags);
        $expected = <<< PHP
if (!$check) {
    $type $name
    {
    }
}
PHP;
        $printer = new Standard();
        $block = wrapClassInIfBlock($stmt, $fqName, $namespace);
        $this->assertEquals($expected, $printer->prettyPrint([$block]));
    }

    /**
     * Should modify private class methods to protected
     *
     * @test
     */
    public function should_modify_private_class_methods_to_protected()
    {
        $stmt = new Class_('Test', [new ClassMethod('privateMethod', ['flags' => Class_::MODIFIER_PRIVATE])]);

        openPrivateClassMethods($stmt);

        $expected = new Class_('Test', [new ClassMethod('privateMethod', ['flags' => Class_::MODIFIER_PROTECTED])]);
        $this->assertCodeEquals($expected, $stmt);
    }

    protected function assertCodeEquals(Stmt $expected, Stmt $actual)
    {
        $printer = new Standard();
        $this->assertEquals($printer->prettyPrint([$expected]), $printer->prettyPrint([$actual]));
    }

    /**
     * Should modify private trait methods to protected
     *
     * @test
     */
    public function should_modify_private_trait_methods_to_protected()
    {
        $stmt = new Trait_('Test', [new ClassMethod('privateMethod', ['flags' => Class_::MODIFIER_PRIVATE])]);

        openPrivateClassMethods($stmt);

        $expected = new Trait_('Test', [new ClassMethod('privateMethod', ['flags' => Class_::MODIFIER_PROTECTED])]);
        $this->assertCodeEquals($expected, $stmt);
    }
}
