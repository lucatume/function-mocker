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

class Extension extends \Codeception\Extension
{

	public static $events = array(
	Events::MODULE_INIT => 'onModuleInit',
	);

	public function onModuleInit(SuiteEvent $event) {
		if (empty($this->config['initFile'])) {
			throw new ExtensionException(__CLASS__, 'You must specify an `initFile` parameter.');
		}

		$initFile = file_exists($this->config['initFile']) ?
			realpath($this->config['initFile'])
			: realpath(getcwd() . '/' . trim($this->config['initFile'], '/\\'));

		if (! is_readable($initFile) || ! is_file($initFile)) {
			throw new ExtensionException(__CLASS__, "[{$initFile}] does not exist, is not a file or is not readable.");
		}

		$suites = ! empty($this->config['suites']) ? (array)$this->config['suites'] : [];

		if (! empty($suites) && ! in_array($event->getSuite()->getName(), $suites, true)) {
			return false;
		}

		include $initFile;

		return true;

	}
}
