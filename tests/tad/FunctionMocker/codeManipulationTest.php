<?php

namespace tad\FunctionMocker;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;

class codeManipulationTest extends TestCase {
	/**
	 * Should correctly resolve global namespaces
	 *
	 * @test
	 */
	public function should_correctly_resolve_global_namespaces() {
		$this->assertEquals('\\foo', resolveNamespace(new Name('foo')));
		$this->assertEquals('\\foo', resolveNamespace(new Name\FullyQualified('\\foo')));
	}

	/**
	 * Should correctly resolve fully-qualified name
	 *
	 * @test
	 */
	public function should_correctly_resolve_fully_qualified_name() {
		$this->assertEquals('\\foo\\bar\\baz', resolveNamespace(new Name\FullyQualified('\\foo\\bar\\baz')));
	}

	/**
	 * Should correctly resolve fully qualified names with fully qualified namespace
	 *
	 * @test
	 */
	public function should_correctly_resolve_fully_qualified_names_with_fully_qualified_namespace() {
		$this->assertEquals(
			'\\foo\\bar\\baz',
			resolveNamespace(
				new Name\FullyQualified('\\foo\\bar\\baz'),
				new Namespace_(new Name\FullyQualified('\\foo\\bar')
				)
			)
		);
	}

	/**
	 * Should correctly resolve fully qualified names with relative namespace
	 *
	 * @test
	 */
	public function should_correctly_resolve_fully_qualified_names_with_relative_namespace() {
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
	public function should_correctly_resolve_relative_names_with_fully_qualified_namespace() {
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
	public function should_correctly_resolve_fully_qualified_name_with_relative_namespace() {
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
	public function should_throw_if_trying_to_get_statements_from_non_file() {
		$this->expectException(\InvalidArgumentException::class);

		getAllFileStmts('foo.php');
	}

	/**
	 * Should allow parsing top level statements in a list of files
	 *
	 * @test
	 */
	public function should_allow_parsing_top_level_statements_in_a_list_of_files() {
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
	public function should_allow_wrapping_statements() {
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
	public function should_correctly_wrap_function_statements_in_if_blocks() {
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
}
