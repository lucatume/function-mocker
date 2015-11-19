<?php
 \Patchwork\Interceptor\deployQueue();/*
 * This file is part of the File_Iterator package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Factory Method implementation that creates a File_Iterator that operates on
 * an AppendIterator that contains an RecursiveDirectoryIterator for each given
 * path.
 *
 * @since     Class available since Release 1.1.0
 */
class File_Iterator_Factory
{
    /**
     * @param  array|string   $paths
     * @param  array|string   $suffixes
     * @param  array|string   $prefixes
     * @param  array          $exclude
     * @return AppendIterator
     */
    public function getFileIterator($paths, $suffixes = '', $prefixes = '', array $exclude = array())
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        if (is_string($paths)) {
            $paths = array($paths);
        }

        $paths   = $this->getPathsAfterResolvingWildcards($paths);
        $exclude = $this->getPathsAfterResolvingWildcards($exclude);

        if (is_string($prefixes)) {
            if ($prefixes != '') {
                $prefixes = array($prefixes);
            } else {
                $prefixes = array();
            }
        }

        if (is_string($suffixes)) {
            if ($suffixes != '') {
                $suffixes = array($suffixes);
            } else {
                $suffixes = array();
            }
        }

        $iterator = new AppendIterator;

        foreach ($paths as $path) {
            if (is_dir($path)) {
                $iterator->append(
                  new File_Iterator(
                    new RecursiveIteratorIterator(
                      new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::FOLLOW_SYMLINKS)
                    ),
                    $suffixes,
                    $prefixes,
                    $exclude,
                    $path
                  )
                );
            }
        }

        return $iterator;
    }

    /**
     * @param  array $paths
     * @return array
     */
    protected function getPathsAfterResolvingWildcards(array $paths)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $_paths = array();

        foreach ($paths as $path) {
            if ($locals = glob($path, GLOB_ONLYDIR)) {
                $_paths = array_merge($_paths, $locals);
            } else {
                $_paths[] = $path;
            }
        }

        return $_paths;
    }
}\Patchwork\Interceptor\deployQueue();
