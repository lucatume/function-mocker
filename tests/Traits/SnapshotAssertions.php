<?php

namespace tad\FunctionMocker\Tests;

trait SnapshotAssertions {

	/**
	 * Checks a file or folder against a snapshot.
	 *
	 * @param string $actual   The relative or absolute path to an output file or folder
	 *                         to check against a snapshot.
	 * @param string $snapshot An optional snapshot to use with a different slug from
	 *                         the $actual basename
	 */
	protected function assertFilesSnapshot( $actual, $snapshot = null ) {
		if ( is_dir( $actual ) ) {
			$this->assertDirectoryExists( $actual );
		} else {
			$this->assertFileExists( $actual );
		}

		$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 1 );
		$cwd = \dirname( $backtrace[0]['file'] );
		$snapshotSlug = $snapshot !== null ? $snapshot : basename( $actual );
		$snapshots = $cwd . '/__snapshots__/' . $snapshotSlug;

		if ( ! file_exists( $snapshots ) ) {
			$this->recurseCopy( $actual, $snapshots );
			$this->markTestSkipped( 'Generated snapshot ' . $snapshots );
		}

		$this->assertFileExists( $snapshots );

		if ( is_dir( $snapshots ) ) {
			$this->assertDirectoryExists( $actual );
			$snapshotPathnames = $this->getDirectoryPathNames( $snapshots );
			$outputPathnames = $this->getDirectoryPathNames( $actual );
		} else {
			$snapshotPathnames = [ $snapshots ];
			$outputPathnames = [ $actual ];
		}

		sort( $snapshotPathnames );
		sort( $outputPathnames );

		$this->assertEquals( $snapshotPathnames, $outputPathnames, 'The files list is not the same' );

		for ( $i = 0, $iMax = \count( $snapshotPathnames ); $i < $iMax; $i ++ ) {
			$snapshotFile = $snapshots . $snapshotPathnames[ $i ];
			$outputFile = $actual . $outputPathnames[ $i ];
			$this->assertFileEquals( $snapshotFile, $outputFile );
		}
	}

	private function recurseCopy( $source, $destination ) {
		if ( ! is_dir( $source ) ) {
			return copy( $source, $destination );
		};

		$dir = opendir( $source );
		mkdir( $destination, 0777, true );
		while ( false !== ( $file = readdir( $dir ) ) ) {
			if ( ( $file !== '.' ) && ( $file !== '..' ) ) {
				if ( is_dir( $source . '/' . $file ) ) {
					$this->recurseCopy( $source . '/' . $file, $destination . '/' . $file );
				} else {
					copy( $source . '/' . $file, $destination . '/' . $file );
				}
			}
		}
		closedir( $dir );
	}

	private function getDirectoryPathNames( $directory, &$pathNames = [] ) {
		$iterator = new \FilesystemIterator( $directory );
		$iterator->setFlags( \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS );
		/** @var \SplFileInfo $file
		 */
		foreach ( $iterator as $file ) {
			if ( $file->isFile() ) {
				$pathNames[] = str_replace( $directory, '', $file->getPathname() );
			}
			if ( $file->isDir() ) {
				$this->getDirectoryPathNames( $file->getPathname(), $pathNames );
			}
		}

		return $pathNames;
	}
}