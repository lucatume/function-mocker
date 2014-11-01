<?php

	namespace tad\FunctionMocker;

	class MockExtender {

		protected static $mockObjectClassName = 'Matcher';

		public static function from( $class ) {
			$extendingClassName = self::createExtensionClass( $class );

			return new $extendingClassName;
		}

		protected static function createExtensionClass( $class ) {
			\Arg::_( $class, 'Class name' )->is_string()
			    ->assert( class_exists( $class ), "Class must exists to be mocked." );

			$template = self::getTemplateContent();
			$mockClassName = self::getMockClassName( $class );
			$vars = array( 'className' => $mockClassName, 'parentClassName' => $class );
			$code = self::render( $vars, $template );

			if ( ! eval( $code ) ) {
				throw new \RuntimeException( 'There was a problem parsing the php code.' );
			}

			return $mockClassName;
		}

		protected static function getTemplateContent() {
			$templateFileName = implode( DIRECTORY_SEPARATOR, array(
				dirname( __FILE__ ), 'templates', 'mock-object.tpl'
			) );

			return file_get_contents( $templateFileName );
		}

		/**
		 * @param $class
		 *
		 * @return string
		 */
		protected static function getMockClassName( $class ) {
			$hash = md5( time() );
			$mockClassName = 'Mock_' . $class . '_' . $hash;

			return $mockClassName;
		}

		protected static function render( array $vars = array(), $template = '' ) {
			array_walk( $vars, function ( $value, $key ) use ( &$template ) {
				$pattern = sprintf( '~\\{%s\\}~', $key );
				$replacement = $value;
				$template = preg_replace( $pattern, $replacement, $template );
			} );

			return $template;
		}


	}
