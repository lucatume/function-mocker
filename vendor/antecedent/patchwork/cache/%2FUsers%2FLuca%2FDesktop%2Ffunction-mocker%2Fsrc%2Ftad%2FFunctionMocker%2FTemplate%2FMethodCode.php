<?php

    namespace tad\FunctionMocker\Template; \Patchwork\Interceptor\deployQueue();

    class MethodCode
    {

        /**
         * @var string
         */
        protected $targetClass;

        /**
         * @var \ReflectionClass
         */
        protected $reflection;

        /**
         * @var array
         */
        protected $methods;

        /** @var  string */
        protected $contents;

        public function setTargetClass($targetClass)
        {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
            $this->targetClass = $targetClass;
            $this->reflection = new \ReflectionClass($targetClass);
            $this->methods = $this->reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            $fileName = $this->reflection->getFileName();
            if (file_exists($fileName)) {
                $this->contents = file_get_contents($fileName);
            }

            return $this;
        }

        public function getTemplateFrom($methodName)
        {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
            $body = '%%pre%% %%body%% %%post%%';

            return $this->getMethodCodeForWithBody($methodName, $body);
        }

        /**
         * @param $methodName
         *
         * @param $body
         *
         * @return array|mixed|string
         */
        protected function getMethodCodeForWithBody($methodName, $body)
        {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
            $code = $this->getMethodCode($methodName);

            $code = $this->replaceBody($body, $code);

            return $code;
        }

        /**
         * @param $methodName
         *
         * @return array|string
         */
        protected function getMethodCode($methodName)
        {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
            $method = is_a($methodName, '\ReflectionMethod') ? $methodName : new \ReflectionMethod($this->targetClass, $methodName);

            $declaringClass = $method->getDeclaringClass();
            $notTargetClass = $declaringClass->name != $this->targetClass;
            if ($notTargetClass) {
                $method = new \ReflectionMethod($declaringClass->name, $methodName);
                $contents = file_get_contents($method->getFileName());
            } else {
                $contents = $this->contents;
            }

            $startLine = $method->getStartLine();
            $endLine = $method->getEndLine();

            $classAliases = [];
            $lines = explode(PHP_EOL, $contents);
            foreach ($lines as $line) {
                $frags = explode(' ', $line);
                if (!empty($frags) && $frags[0] == 'use') {
                    $fullClassName = $frags[1];
                    // use Acme\Class as Alias
                    if (count($frags) > 2) {
                        $alias = $frags[3];
                    } else {
                        if (strpos($frags[1], '\\')) {
                            $classNameFrags = explode('\\', $frags[1]);
                            $alias = array_pop($classNameFrags);
                        } else {
                            $alias = $frags[1];
                        }
                    }
                    $alias = trim($alias, ';');
                    $classAliases[$alias] = trim($fullClassName, ';');
                }
            }

            $lines = array_map(function ($line) use ($classAliases) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
                foreach ($classAliases as $classAlias => $fullClassName) {
                    $line = str_replace($classAlias, $fullClassName, $line);
                }
                return trim($line);
            }, $lines);

            $code = array_splice($lines, $startLine - 1, $endLine - $startLine + 1);

            $code[0] = preg_replace('/\\s*abstract\\s*/', '', $code[0]);

            $code = implode(" ", $code);

            return $code;
        }

        /**
         * @param $body
         * @param $code
         *
         * @return mixed
         */
        protected function replaceBody($body, $code)
        {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
            $code = preg_replace('/\\{.*\\}$|;$/', '{' . $body . '}', $code);
            $code = preg_replace('/\\(\\s+/', '(', $code);
            $code = preg_replace('/\\s+\\)/', ')', $code);

            return $code;
        }

        public function getAllMockCallings()
        {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
            $code = array_map(function ($method) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
                return $this->getMockCallingFrom($method);
            }, $this->methods);
            $code = implode("\n\n\t", $code);

            return $code;
        }

        public function getMockCallingFrom($methodName)
        {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
            $method = is_a($methodName, '\ReflectionMethod') ? $methodName : new \ReflectionMethod($this->targetClass, $methodName);
            $methodName = is_string($methodName) ? $methodName : $method->name;
            $args = array_map(function (\ReflectionParameter $parameter) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
                return '$' . $parameter->name;
            }, $method->getParameters());
            $args = implode(', ', $args);
            $body = "return \$this->__functionMocker_originalMockObject->$methodName($args);";

            return $this->getMethodCodeForWithBody($methodName, $body);
        }
    }\Patchwork\Interceptor\deployQueue();
