<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 25/04/15
 * Time: 22:23
 */

namespace Another\Acme; \Patchwork\Interceptor\deployQueue();


use Acme\Some\Class1;

class Class3 {

	public function testMethod( Class1 $param ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);

	}
}\Patchwork\Interceptor\deployQueue();