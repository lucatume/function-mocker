<?php
/**
 * Represents a FunctionMocker CLI runtime exception.
 *
 * Any exception that is not about a wrong on incoherent user input
 * is considered a runtime one.
 *
 * @package    FunctionMocker
 * @subpackage CLI
 * @author     Luca Tumedei <luca@theaveragedev.com>
 * @copyright  2018 Luca Tumedei
 */

namespace tad\FunctionMocker\CLI\Exceptions;

class RuntimeException extends \RuntimeException
{

    /**
     * Returns an exception when the command that's currently running almost
     * reached the available memory limit.
     *
     * @return \tad\FunctionMocker\CLI\Exceptions\RuntimeException
     */
    public static function becauseTheCommandAlmostReachedMemoryLimit()
    {
        $message = 'Memory limit almost reached: use more stringent criteria for the source to avoid this.';

        return new static($message);
    }

    /**
     * Returns an exception when the command that's currently running almost
     * reached the available time limit.
     *
     * @return \tad\FunctionMocker\CLI\Exceptions\RuntimeException
     */
    public static function becauseTheCommandAlmostReachedTimeLimit()
    {
        $message = 'Time limit almost reached: use more stringent criteria for the source to avoid this.';

        return new static($message);
    }

    /**
     * Returns an exception when the PHP version required by the component is not met.
     *
     * @param string $what         The name of the component to check the version for.
     * @param string $whichVersion The version of PHP to check.
     *
     * @return \tad\FunctionMocker\CLI\Exceptions\RuntimeException
     */
    public static function becauseMinimumRequiredVersionIsNotMet($what, $whichVersion)
    {
        $message = sprintf('%s requires PHP >=%s', $what, $whichVersion);

        return new static($message);
    }

    /**
     * Returns an error to signal that no sources were specified in the command arguments
     * and found in a configuration file.
     *
     * @return \tad\FunctionMocker\CLI\Exceptions\RuntimeException
     */
    public static function becauseNoSourcesWereSpecified()
    {
        $message = 'Specify at least one source file or folder in the CLI arguments or the configuration file.';

        return new static($message);
    }
}
