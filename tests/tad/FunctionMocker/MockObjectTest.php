<?php

namespace tad\FunctionMocker\Tests;


use tad\FunctionMocker\MockObject;

class MockObjectTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @test
	 * it should call Matcher for each method
	 */
	public function it_should_call_matcher_for_each_method() {
		$sut = new MockObject();
		$matcher = $this->getMock('\tad\FunctionMocker\Matcher');
		$matcher->expects($this->once())->method('wasCalledTimes');
		$sut->__init($matcher);
		$sut->wasCalledTimes(4);
	}

}