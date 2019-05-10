<?php
/**
 * Function Mocker extension class to integrate with wp-browser.
 *
 * @package    FunctionMocker
 * @subpackage Integrations\wp-browser
 * @author     Luca Tumedei <luca@theaveragedev.com>
 * @copyright  2018 Luca Tumedei
 */

namespace tad\FunctionMocker\WPBrowser;

use Codeception\Event\SuiteEvent;
use Codeception\Events;
use Codeception\Exception\ExtensionException;

/**
 * Class Extension
 */
class Extension extends \Codeception\Extension
{

    /**
     * A map of events the extension will listen for and react to.
     *
     * @var array
     */
    public static $events = array(
        Events::MODULE_INIT => 'onModuleInit',
    );

    /**
     * When the modules initialize then the extension will load an ad-hoc initialization file.
     *
     * @param SuiteEvent $event The current suite event.
     *
     * @return boolean Whether the initialization file was loaded or not.
     *
     * @throws ExtensionException If no initialization file path is specified or the specified path is not valid.
     */
    public function onModuleInit(SuiteEvent $event)
    {
        if (empty($this->config['initFile'])) {
            throw new ExtensionException(__CLASS__, 'You must specify an `initFile` parameter.');
        }

        $initFile = file_exists($this->config['initFile']) ?
            realpath($this->config['initFile'])
            : realpath(getcwd() . '/' . trim($this->config['initFile'], '/\\'));

        if (!is_readable($initFile) || !is_file($initFile)) {
            throw new ExtensionException(__CLASS__, "[{$initFile}] does not exist, is not a file or is not readable.");
        }

        $suites = !empty($this->config['suites']) ? (array)$this->config['suites'] : [];

        if (!empty($suites) && !in_array($event->getSuite()->getName(), $suites, true)) {
            return false;
        }

        include $initFile;

        return true;
    }
}
