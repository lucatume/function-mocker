<?php

	namespace tad\FunctionMocker;

	class MockExtender {

		protected static $mockObjectClassName = 'Matcher';
		protected        $smarty;
		protected        $mockedClasses       = array();

		public static function from( $class ) {
			$instance = new self;
			$instance->initSmarty();

			$extendingClassName = $instance->createExtensionClass( $class );

			return new $extendingClassName;
		}

		public function createExtensionClass( $class ) {
			\Arg::_( $class, 'Class name' )->is_string()
			    ->assert( class_exists( $class ), "Class must exists to be mocked." );

			if ( in_array( $class, array_keys( $this->mockedClasses ) ) ) {
				return $this->mockedClasses[ $class ];
			}

			$reflection = new \ReflectionClass( $class );
			$classShortName = $reflection->getShortName();
			$className = $reflection->getName();
			$mockClassName = $this->getMockClassName( $classShortName );
			$vars = array( 'className' => $mockClassName, 'parentClassName' => $className );

			$code = $this->getMockObjectCode( $vars );

			if ( eval( $code ) === false ) {
				throw new \RuntimeException( 'There was a problem parsing the php code; where is a mistery.' );
			}

			$this->mockedClasses[ $class ] = $mockClassName;

			return $mockClassName;
		}

		/**
		 * @param $class
		 *
		 * @return string
		 */
		protected function getMockClassName( $class ) {
			$hash = md5( time() );
			$mockClassName = 'Mock_' . $class . '_' . $hash;

			return $mockClassName;
		}

		protected function initSmarty() {
			$this->smarty = new \Smarty();
			$this->smarty->setCaching( \Smarty::CACHING_LIFETIME_CURRENT );
			$templateDir = dirname( __FILE__ ) . '/templates';
			$this->smarty->setTemplateDir( $templateDir );
			$this->smarty->setCompileDir( $templateDir . '/compiled' );
			$this->smarty->setCacheDir( $templateDir . '/cache' );
		}

		protected function getMockObjectCode( array $vars ) {
			$smarty = $this->smarty;
			array_walk( $vars, function ( $value, $key ) use ( $smarty ) {
				$smarty->assign( $key, $value );
			} );
			$code = $this->smarty->fetch( 'mock-object.tpl' );

			return $code;
		}


	}
