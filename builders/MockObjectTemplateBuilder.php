<?php

	namespace tad\FunctionMocker;


	class MockObjectTemplateBuilder extends \PHPUnit_Framework_TestCase {

		protected $mockObjectClassName = 'Matcher';

		/**
		 * @test
		 */
		public function createMockObjectTemplate() {
			$mockObjectFileName = $this->getMockObjectFileName();
			$templateFileName = $this->getTemplateFileName( $mockObjectFileName );
			$mockObjectCode = $this->getMockObjectCode( $mockObjectFileName );
			$mockObjectCode = $this->addExtendsTemplate( $mockObjectCode );
			$mockObjectCode = $this->templatizeClassName( $mockObjectCode );
			$mockObjectCode = $this->removeComments( $mockObjectCode );
			$mockObjectCode = addslashes($mockObjectCode);

			file_put_contents( $templateFileName, $mockObjectCode );
		}

		/**
		 * @return string
		 */
		protected function getMockObjectFileName() {
			$reflection = new \ReflectionClass( '\tad\FunctionMocker\MockObject' );
			$mockObjectFileName = $reflection->getFileName();

			return $mockObjectFileName;
		}

		/**
		 * @param $mockObjectFileName
		 *
		 * @return string
		 */
		protected function getTemplateFileName( $mockObjectFileName ) {
			$templateFileName = implode( DIRECTORY_SEPARATOR, array(
				dirname( $mockObjectFileName ), 'templates', 'mock-object.tpl'
			) );

			return $templateFileName;
		}

		/**
		 * @param $mockObjectFileName
		 *
		 * @return mixed|string
		 */
		protected function getMockObjectCode( $mockObjectFileName ) {
			$mockObjectCode = @file_get_contents( $mockObjectFileName );

			$mockObjectCode = preg_replace( '/\\<\\?php\s*/', '', $mockObjectCode );

			return $mockObjectCode;
		}

		/**
		 * @param $mockObjectCode
		 *
		 * @return mixed
		 */
		protected function addExtendsTemplate( $mockObjectCode ) {
			$pattern = sprintf( '/\\s%s\\s/', $this->mockObjectClassName );
			$replacement = sprintf( ' %s extends {parentClassName} ', $this->mockObjectClassName );
			$mockObjectCode = preg_replace( $pattern, $replacement, $mockObjectCode );

			return $mockObjectCode;
		}

		/**
		 * @param $mockObjectCode
		 *
		 * @return mixed
		 */
		protected function templatizeClassName( $mockObjectCode ) {
			$pattern = sprintf( '/%s/', $this->mockObjectClassName );
			$replacement = '{className}';
			$mockObjectCode = preg_replace( $pattern, $replacement, $mockObjectCode );

			return $mockObjectCode;
		}

		/**
		 * @param $mockObjectCode
		 *
		 * @return mixed
		 */
		protected function removeComments( $mockObjectCode ) {
			$mockObjectCode = preg_replace( '!/\*.*?\*/!s', '', $mockObjectCode );
			$mockObjectCode = preg_replace( '/\n\s*\n/', "\n", $mockObjectCode );

			return $mockObjectCode;
		}
	}